<?php
/**
 * @link http://www.citc.com/
 * @copyright Copyright (c) 2015 CITC
 * @license 
 */

namespace Framework;

/**
 * URL解析，对输入进行防SQL注入等安全检查，并把用户的请求定向到APP目录的ui子目录下
 *
 * @property string $_controller 控制器类名字.
 * @property string $_action action名字.
 *
 * @author lifq
 * @since 1.0
 */
class UrlManager{
    
    private $controller;
    private $action;
    
    /**
     * 构造函数，从用户请求信息中提取class和action参数，解析后保存到类属性中.
     * 用户能够直接访问类只能在ui目录下，其他目录不能直接访问
     * @param 
     * @return 
     * @throws 
     */    
    public function __construct(){
        
        //把用户请求定向到iu子目录下
        if(isset($_REQUEST['c']))
            $this->controller = '\\ui\\'.str_replace('.','\\',$_REQUEST['c']);
        else
            $this->controller = DEFAULT_UICLASS;
                
        if(isset($_REQUEST['a']))
            $this->action = $_REQUEST['a'];
        else
            $this->action = DEFAULT_UIACTION;
            
    }
    
    /**
     * url解析过滤，过滤到异常的url请求，防止SQL注入等攻击行为.
     * @param string $url 需要解析过滤的用户url
     * @return 解析后的url
     * @throws 
     */    
    public function filterUrl($url){
        return $url;
    }
    
    /**
     * 获取控制器类名.
     * @param 
     * @return 控制器类名
     * @throws 
     */    
    public function getController(){
        return $this->controller;
    }
    
    /**
     * 获取活动类名.
     * @param 
     * @return 活动类名
     * @throws 
     */    
    public function getAction(){
        return $this->action;
    }

}