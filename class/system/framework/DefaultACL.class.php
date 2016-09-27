<?php
/**
 * @link http://www.citc.com/
 * @copyright Copyright (c) 2015 CITC
 * @license 
 */
namespace Framework;

/**
 * 缺省的AccessControl类
 *
 * @property
 *
 * @author lifq
 * @since 1.0
 */
class DefaultACL extends \Framework\AccessControl{

    
    /**
     * 实现抽象接口，子类必须实现，返回本controler对应的模块名称，用于权限管理.
     * @param string $user 用户名
     * @param Controller的派生类 $controller 控制器
     * @param action $action 动作
     * @return boolean 缺省情况下，都返回TRUE，不做权限判断
     * @throws 
     */     
     public function isAllow($controller,$action='default'){
        return TRUE;
    }

}

?>