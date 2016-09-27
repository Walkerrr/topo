<?php
/**
 * @link http://www.citc.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.citc.com/
 */
namespace ui;

/**
 * 用户登录类，不需要用户登录即可访问该类，属于特殊权限的类
 *
 * @property User $user 本地会话对象
 *
 * @author lifq
 * @since 1.0
 */
class Login extends \framework\Controller
{
    private $user;
    
    public function __construct(){
        $this->user = new \user\User();     
    }

    public function getParamInfoInterface()
    {
		$actionParam=array(
			"loginAction"=>array("username"=>"string","password"=>"string"),
			"logoutAction"=>array(),
			"statusAction"=>array()
		);
		return $actionParam;

    }

    //登录操作
    public function loginAction($username,$password)
	{

        if($this->user->login($username,$password))
            return \Framework\Result::OK('登录成功！');
        else
            return \Framework\Result::Error('登录失败，请检查用户名和密码是否正确！');
    }    

    /**
     * 用户退出
     */     
    //登出操作
    public function logoutAction(){
        $this->user->logout();
        return \Framework\Result::OK('logout action is ok!');
    }
    
    /**
     * 查询用户登录状态
     */     
    public function statusAction(){
        return $this->user->status();
    }
        
}
?>