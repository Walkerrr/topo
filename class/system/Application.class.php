<?php
/**
 * @link http://www.citc.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.citc.com/
 */
 //引入框架默认配置
require_once __DIR__.'/config-default.inc.php';

/**
 * 应用类，所有应用的起点。
 *
 * @property Application $_instance 类的唯一实例
 *
 * @author lifq
 * @since 1.1
 */
class Application{
    private static $_instance = null;
    private $logger; 	
	
    private function __construct(){
        $this->logger = \Logger::getLogger( __CLASS__ );		
    }    

    /**
     * 返回当前对象实例，静态函数，保证只创建一个实例，只能通过该函数生成Application对象，不能通过构造函数创建.
     * @return 唯一的Application实例
     * @throws 
     */      
    public static function getInstance()       
    {       
        if (null == self::$_instance)       
        {       
            self::$_instance = new self();       
        }       
        return self::$_instance;       
    }       
    
    /**
     * 自动load函数，new对象的时候自动调用该函数.
     * @param string $className 类名称
     * @return 
     * @throws 
     */    
    public static function autoLoad($className){

        //首先在系统目录搜索类
    	$classPath=__DIR__.'/'.$className.'.class.php';
    	if(file_exists($classPath)){
    		require_once $classPath;
            return;
    	}
        
        //然后在应用目录搜索类
    	$classPath=__DIR__.'/../'.APP_NAME.'/'.$className.'.class.php';
    	if(file_exists($classPath)){
    		require_once $classPath;
            return;
    	}

        //如果都搜索不到，则返回错误提示
        if (!class_exists($className, false)) {
            \Logger::getLogger( __CLASS__ )->error( __LINE__ .' '. __FUNCTION__ .' '.'在'.$classPath.'目录下无法找到'.$className.'类'); 
        }
        
    }
    
    /**
     * 程序总的启动入口，启动时调用.
     * @param string $className 类名称
     * @return Result jsong编码的Result对象
     * @throws 
     */    
    public function run(){
        $url = new \framework\UrlManager;
        $controller = $url->getController();
        $action = $url->getAction();

        $ctrl = new $controller;

        //权限判断
        $acl_class = ACL_CLASS;
        $acl = new $acl_class;
        if($acl->isAllow($ctrl,$action))
		{
			
			//计算Action的执行时间
			$start_dt = time();
			$mfrFlag ='0';
			if( isset( $_REQUEST["MFR"] ) ) //MR=Must Fromat Result,框架必须格式化结果
			{
				$mfrFlag =  $_REQUEST["MFR"];
			}

            $ret = $ctrl->startAction($action);

            $dt = time()-$start_dt;
            $this->logger->trace( __LINE__ .' '. __FUNCTION__ .' '.'控制类'.$controller.'运行'.$action.'方法耗时'.$dt.'秒'); 
                        
            //返回的结果必须为Result类型
            if($ret instanceof \framework\Result){
                return json_encode($ret);
            }
            else{

				if("1"!=$mfrFlag)
				{
					//此处需要验证
					return $ret;
				}
				else
				{
					return json_encode(\framework\Result::Error($ret,null));
				}
            }
        }
        else{
            return json_encode(\framework\Result::Error('您无权访问控制类：['.$controller.']！'));
        }
              
    }
	
}

/**
 * 注册自动类加载函数.
 */
spl_autoload_register(array('Application','autoLoad'));  

?>