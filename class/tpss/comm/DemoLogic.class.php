<?php  
/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.dimpt.com/
 */
namespace comm;

/**
 * 应用逻辑类样例
 *
 *
 * @author songqj
 * @since 1.0
 * 
 */
class DemoLogic {  

    private $logger;                   //日志对象
    private $pdodb = null;              //数据库连接句柄
      
    /**
     * 构造函数.
     * @param 
     * @throws 
     */ 
    public function __construct() 
    {  
        $this->logger = \Logger::getLogger( __CLASS__ );
        $this->pdodb= \db\PdoDB::getInstance();	

    }  

    /**
     * 获取省、本地网、汇聚区的节点树，用于支持树控件
     * @param string $nodeID 父节点ID，默认为顶层节点
     * @return 指定节点下的子节点，以JSON格式返回
     */      
    public function demo($name,$hellomsg) 
    {  	
		
		$retStr = "你好".$name.":\n欢迎加入中讯支撑平台开发团队。\n".$hellomsg;
		
		return $retStr;
    } 


}
	
?>  