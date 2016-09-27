<?php
/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.dimpt.com/
 */
namespace ui;

/**
 * Controller派生类，实现和用户管理相关的UI操作
 *
 * @property User $user 本地会话对象
 *
 * @author lifq
 * @since 1.0
 */
class UserManager extends \framework\Controller{
    private $user;
    
    public function __construct(){
        $this->user = new \user\User();     
    }
    
    public function getParamInfoInterface()
    {
		$actionParam=array(
			"userinfoAction"=>array(),
			"editAction"=>array("fullname"=>"string","email"=>"string","mobilephone"=>"string",
			          "department"=>"string","defaultregion"=>"string"),
			"isAdminAction"=>array(),
			"updateAction"=>array("username"=>"string","fullname"=>"string","email"=>"string","level"=>"string",
			           "defaultregion"=>"string","mobilephone"=>"string","isadmin"=>"string",
			           "department"=>"string","enabled"=>"string"),
			"addAction"=>array("username"=>"string","fullname"=>"string","email"=>"string","level"=>"string",
			           "defaultregion"=>"string","mobilephone"=>"string","isadmin"=>"string",
			           "department"=>"string","enabled"=>"string"),
			"deleteAction"=>array("username"=>"string"),
			"modifyPwdAction"=>array("newpwd"=>"string","oldpwd"=>"string"),
			"resetPwdAction"=>array("username"=>"string","email"=>"string"),
			"activateUserAction"=>array("username"=>"string"),
			"listAction"=>array("page"=>"int","rows"=>"int","queryname"=>"string"),
			"statusAction"=>array()


		);
		return $actionParam;

    }



    /**
     * 查询用户详细信息.
     * @param 
     * @return Result对象
     * @throws 
     */     
    public function userinfoAction(){
        $userinfo = $this->user->getUserInfo();
        if(null == $userinfo)
            return \Framework\Result::Error();
        else
            return \framework\Result::OK('',$userinfo);
    }   
    
    /**
     * 编辑用户个人信息.
     * @param 
     * @return Result对象
     * @throws 
     */     
    public function editAction($fullname,$email,$mobilephone,$department,$defaultRegion){
        if($fullname==null || $email==null)
            return \Framework\Result::Error('用户全名和电子邮箱地址不能为空！');
            
        if($this->user->editUserInfo($fullname,$email,$mobilephone,$department,$defaultRegion)){
            return \Framework\Result::OK("用户信息更改成功");
        }
        else{
            return \Framework\Result::Error();
        }
    } 
    
    /**
     * 判断用户是否是超级用户.
     * @param 
     * @return Result对象
     * @throws 
     */     
    public function isAdminAction(){
        if(true == $this->user->isAdmin())
            return \Framework\Result::OK();
        else
            return \framework\Result::Error();
    }
    

        

    /*******************************以下函数需超级用户权限才能操作***************************************/    
    /**
     * 修改其他用户的信息，必须拥有超级用户权限才能操作.
     * @param 
     * @return Result对象
     * @throws 
     */     
    public function updateAction($username,$fullname,$email,$level,$defaultregion,
		                              $mobilephone,$isadmin,$department,$enabled)
	{
        if(!$this->user->isAdmin())
            return \framework\Result::Error();
            
        $this->user->updateUserInfo($username,$fullname,$email,$level,$defaultregion,
			                          $mobilephone,$isadmin,$department,$enabled);   
            
        return \Framework\Result::OK();
    }  
    
    /**
     * 返回用户列表，必须拥有超级用户权限才能操作.
     * @param 
     * @return Result对象
     * @throws 
     */     
    public function listAction($page,$rows,$queryname)
	{
        if(!$this->user->isAdmin())
            return \framework\Result::Error("你没有权限获取用户列表！！");

        //$ret = \Framework\Result::OK();
        //$ret->data = $this->user->getUserList();
     	return json_encode( $this->user->getUserList($page,$rows,$queryname));
    }
    
    /**
     * 增加用户，必须拥有超级用户权限才能操作.
     * @param 
     * @return Result对象
     * @throws 
     */     
    public function addAction($username,$fullname,$email,$level,$defaultregion,
			                          $mobilephone,$isadmin,$department,$enabled)
	{
        if(!$this->user->isAdmin())
            return \framework\Result::Error();

        $this->user->addUser($username,$fullname,$email,$level,$defaultregion,
			                          $mobilephone,$isadmin,$department,$enabled);
     	return \Framework\Result::OK();
    }

    /**
     * 删除用户，必须拥有超级用户权限才能操作.
     * @param 
     * @return Result对象
     * @throws 
     */     
    public function deleteAction($username){
        if(!$this->user->isAdmin())
            return \framework\Result::Error();

        $this->user->delUser($username);
     	return \Framework\Result::OK();
    }

    /**
     * 临时阻止用户，必须拥有超级用户权限才能操作.
     * @param 
     * @return Result对象
     * @throws 
     */     
    public function blockUserAction($username){
        if(!$this->user->isAdmin())
            return \framework\Result::Error();

        $this->user->blockUser($username);
     	return \Framework\Result::OK();
    }
    /**
     * 激活用户，必须拥有超级用户权限才能操作.
     * @param 
     * @return Result对象
     * @throws 
     */     
    public function activateUserAction($username){
        if(!$this->user->isAdmin())
            return \framework\Result::Error();

        $this->user->activateUser($username);
     	return \Framework\Result::OK();
    }


    /**
     * 重置用户口令
     */     
    public function resetPwdAction($username,$email){

        $ret=$this->user->resetUserPwd($username,$email);
     	return \Framework\Result::OK($ret);
    }
    /**
     * 修改自身口令.
     * @param 
     * @return Result对象
     * @throws 
     */     
    public function modifyPwdAction($newpwd,$oldpwd){
     	return \Framework\Result::OK($this->user->modifyPwd($newpwd,$oldpwd));
    }
		


}
?>