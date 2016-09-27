<?php

namespace Framework;

class DefaultUI extends \Framework\Controller{
    
    public function __construct(){
    }
    
    /**
     * 设置当前sheet名称函数.
     * @param string $sheetName sheet名
     * @return 
     * @throws 
     */     
    public function aclGetModuleName(){
        return '';        
    }

    /**
     * 抽象接口，子类必须实现，返回本controler对应的访问数据，用于权限管理.
     * @param 
     * @return 本模块对应的名称
     * @throws 
     */    
    public function aclGetDataScope(){
        return '';
    }
    
    public function defaultAction(){
        return new \Framework\Result(TRUE,'default action!');
    }
    
}

?>