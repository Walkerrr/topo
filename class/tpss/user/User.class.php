<?php
/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license 
 */
namespace user;

/**
 * 用户管理类，实现用户登录管理
 *
 * @property Session $session 本地会话对象
 *
 * @author lifq
 * @since 1.0
 */
class User{
    private $sess;
    private $dao;
    
    public function __construct(){
        $this->sess = \framework\Session::getInstance();
        $this->dao = new \dao\DaoUser();
    }
    
    /**
     * 用户登录函数.
     * @param string $userName 用户名
     * @param string $psw 用户密码
     * @return boolean 登录成功返回TRUE，否则返回FALSE
     * @throws 
     */     
	public function login($userName,$psw)
	{
        return $this->dao->login($userName,$psw);
	   
	}
    
    /**
     * 返回用户的登录状态.
     * 用session中login_userName变量判断用户是否登录
     * @param 
     * @return boolean 登录返回TRUE，否则返回FALSE
     * @throws 
     */     
    public function isLogin(){
        if(isset($this->sess->login_userName)){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * 判断用户是否是系统管理员.
     * 如果t_User表中的level="1"
     * @param 
     * @return boolean 登录返回TRUE，否则返回FALSE
     * @throws 
     */     
    public function isAdmin(){
        $userInfo = $this->getUserInfo();
        if("1" == $userInfo[0]->isadmin)
		{
            return true;
        }
        else{
            return false;
        }
    }
    
    /**
     * 用户退出.
     * @param 
     * @return boolean 登录返回TRUE，否则返回FALSE
     * @throws 
     */     
    public function logout(){
        $this->sess->destroy();
    }

    /**
     * 更新用户状态，并返回登录状态.
     * @param 
     * @return Result 已登录返回TRUE，否则返回FALSE
     * @throws 
     */     
    public function status(){
            return $this->dao->status();
    }    

    /**
     * 获取用户的全部信息.
     * @param 
     * @return Result 已登录返回TRUE，否则返回FALSE
     * @throws 
     */     
    public function getUserInfo()
    {
        return $this->dao->getUserInfo();
    }    

    /**
     * 获取用户名.
     * @param 
     * @return Result 已登录返回TRUE，否则返回FALSE
     * @throws 
     */     
    public function getUserName()
    {
        return $this->sess->login_userName;
    } 
    
     
    /**
     * 编辑用户本人的信息.
     * @param 
     * @return Result 已登录返回TRUE，否则返回FALSE
     * @throws 
     */     
    public function editUserInfo($FullName,$email,$mobilePhone,$department,$defaultRegion)
    {

        //如果未登录，则直接返回
        if(!$this->isLogin())
            return false;

        return $this->dao->editUserInfo($FullName,$email,$mobilePhone,$department,$defaultRegion);
        
    }

    /**
     * 编辑其他用户信息，必须拥有超级用户权限，否则不允许.
     * @param 
     * @return Result 已登录返回TRUE，否则返回FALSE
     * @throws 
     */     
    public function updateUserInfo($userName,$fullName,$email,$level,$defaultRegion,$mobilePhone,$isAdmin,$department,$enabled)
	{
        if($fullName == '')
            $fullname = $userName;

            
        $this->dao->updateUserInfo($userName,$fullName,$email,$level,$defaultRegion,$mobilePhone,$isAdmin,$department,$enabled);

    }
    
    /**
     * 返回系统中的用户列表，必须拥有超级用户权限，否则不允许.
     * @param 
     * @return Array 用户列表
     * @throws 
     */   
    public function getUserList($page,$rows,$queryname)
    {
		$retdata=array();
        $result = $this->dao->getAllUserList($queryname);
		$retdata["total"]=count($result);
        //$retdata["rows"] = $result;   
        $retdata["rows"] = array_slice($result,($page-1)*$rows,$rows);   
        return $retdata;

    }  
    
    /**
     * 增加用户，必须拥有超级用户权限，否则不允许.
     * @param string $userName 增加的用户名
     */       
    public function addUser($userName,$fullName,$email,$level,$defaultRegion,$mobilePhone,$isAdmin,$department,$enabled)
    {
        $this->dao->addUser($userName,$fullName,$email,$level,$defaultRegion,$mobilePhone,$isAdmin,$department,$enabled);
    }  

    /**
     * 删除用户，必须拥有超级用户权限，否则不允许.
     * @param string $userName 删除的用户名
     */     
    public function delUser($userName)
    {
        $this->dao->enabledUser($userName,"9");
    }    
    /**
     * 激活用户，必须拥有超级用户权限，否则不允许.
     * @param string $userName 激活的用户名
     */     
    public function activateUser($userName)
    {
        $this->dao->enabledUser($userName,"1");
    }

    public function blockUser($userName)
    {
        $this->dao->enabledUser($userName,"0");
    }    


    public function resetUserPwd($userName,$email)
    {
        $mailtool = new \utils\MailTool();
        $ret=$mailtool->ResetAuthKey($userName,$email);
		return($ret);
    }    

    /**
     * 修改用户口令
     * @param string $userName,$newPwd,$oldPwd
     */ 
    public function modifyPwd($newPwd,$oldPwd)
    {
        return($this->dao->modifyUserPwd($newPwd,$oldPwd));
    }    


}

?>