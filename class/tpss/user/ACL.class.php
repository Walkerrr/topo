<?php
/**
 * @link http://www.citc.com/
 * @copyright Copyright (c) 2015 CITC
 * @license 
 */
namespace user;

/**
 * AccessControl派生类，实现具体的权限判断
 *
 * @property Session $session 本地会话对象
 *
 * @author lifq
 * @since 1.0
 * @change:2015-06-30增加了匿名方法检查，允许某些类的某些方法在不登录情况下访问
 */
class ACL extends \framework\AccessControl{
    private $user = null;
    
    public function __construct(){
        $this->user = new \user\User();         
    }
    
    /**
     * 实现抽象接口，子类必须实现，返回本controler对应的模块名称，用于权限管理.
     * @param string $user 用户名
     * @param Controller的派生类 $controller 控制器
     * @return boolean 缺省情况下，都返回TRUE，不做权限判断
     * @throws 
     */     
    public function isAllow($controller,$action='default'){
        //如果登录管理类，则不需要进行权限判断，用户可以
        if($this->isLoginClass($controller)){
            //TODO:
            return true;
        }

        //判断是否为匿名访问方法，如果是则不需要进行权限判断
        if($this->isAnonymousAction($controller,$action)){
            return true;
        }
        //如果已经登录，则判断用户是否有权限访问
        else if($this->user->isLogin())
		{
            //对于系统管理，不进行权限判断
            if($this->user->isAdmin())
			{
                return true;
            }
            
			return true;

            //TODO:
			//此样例未包含模块授权检查
        }
        else{
            return false;
        }
    }
    
    /**
     * 返回用户的登录状态.
     * 用session中userName变量判断用户是否登录
     * @param 
     * @return boolean 登录返回TRUE，否则返回FALSE
     * @throws 
          
    public function isLogin(){
        return $this->user->isLogin();
    }*/

    /**
     * 判断当前控制器类是否是用户登录用的类，如果是返回true;否则返回false.
     * @param 
     * @return boolean 登录返回TRUE，否则返回FALSE
     * @throws 
     */     
    public function isLoginClass($class){
        $className = get_class($class);
        if(0 == strcmp($className,LOGIN_CLASS))
            return true;
        else
            return false;
    }

    /**
     * 判断当前控制器类及方法是否为特例，即允许不登陆即可访问，如果是返回true;否则返回false.
     * @param 
     * @return boolean 登录返回TRUE，否则返回FALSE
     * @throws 
     */     
    public function isAnonymousAction($class,$action){
        $className = get_class($class);
	    //对移动客户端另外进行授权控制
		if($className=="ui\\IPhoneDeal")
		{
			return true;
		}
		$allowAction = array(
			"ui\\FreeGrid"=>array("getHelpCatalog","getHelpItemByCatalogID","getHelpDetailByID","getHelpItemByKeyword"),
			"ui\\GisServerOP"=>array("getSingleGeoJSON","getGeoJSON","saveSingleGeoJSON"),
			"ui\\UserManager"=>array("resetPwd")
			);
		if(array_key_exists ($className,$allowAction))
		{
			if(in_array($action,$allowAction[$className]))
			{
				return true;
			}
		}
        return false;
    }
}

?>