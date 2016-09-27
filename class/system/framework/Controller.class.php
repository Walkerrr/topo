<?php
/**
 * @link http://www.citc.com/
 * @copyright Copyright (c) 2015 CITC
 * @license 
 */
namespace framework;

/**
 * 控制器类的基类，抽象类
 *
 * @property
 *
 * @author lifq
 * @since 1.0
 * 2015-04-22 1.增加了getParamInfoInterface，框架获取action函数参数列表后，自动进行参数转换。 songqj
 * 2015-06-22 1.修改了参数传入方式，可以支持action函数中使用默认值，暂不开放使用。 songqj
 */
class Controller{
    protected $logger; 	
	
    public function __construct(){
        $this->logger = \Logger::getLogger( __CLASS__ );		
    }    
    
    /**
     * 子类可以继承，返回本controler对应的模块名称，用于权限管理.
     * 缺省情况返回ALL，就是不限制对该模块的访问，如果子类不继承该函数，则不做权限限制。
     * @param 
     * @return 本模块对应的名称
     * @throws 
     */    
    public function aclGetModuleName(){
        return 'ALL';
    }

    /**
     * 子类可以继承，返回本controler对应的访问数据，用于权限管理.
     * 缺省情况返回{}，就是不限制该模块对数据的访问，如果子类不继承该函数，则不做数据权限限制。
     * @param 
     * @return 本模块对应的名称
     * @throws 
     */    
    public function aclGetDataRules(){
        return '{}';
    }


    /**
     * 子类不应该继承，保证每个类都有默认处理
     * @param 
     * @return 本模块对应的名称
     * @throws 
     */    
    public function defaultAction()
	{	 
		return \Framework\Result::Error('请求中未设置正确的action');    
	}

    /**
     * 子类可以继承，用于获取对外参数接口定义
     * @param 
     * @return 控制类各个action对应的参数名称及数据类型(string,bool,int,float,json)
     * @throws 
     */    
    public function getParamInfoInterface()
    {
        return array();
        //样例代码如下：
		$actionParam=array(
			"insertRecordAction"=>array("keyID"=>"string","keyName"=>"string","maxV"=>"int"),
			//...
			"deleteRecordAction"=>array("keyID"=>"string")
		);
		return $actionParam;

    }
	 
    /**
     * 启动action，根据action输入，调用子类相应的action成员函数.
     * @param string $action 需要启动活动名称
     * @return 子类action成员函数的返回结果
     * @throws 
     */    
    public function startAction($action){

        $method = $action.'Action';
        if(!method_exists($this, $method)) { 
			$this->logger = \Logger::getLogger( __CLASS__ );		
            $this->logger->warn( __LINE__ .' '. __FUNCTION__ .' '.'无法找到'.$method.'方法'); 
            $method = 'defaultAction';
        }
		//读取控制器类的参数定义信息
		$paramDefineArray = $this->getParamInfoInterface();
        $http =  \utils\HttpParam::getInstance();

		$parray = array();
		//自动从$_REQUEST获取参数并传给action函数
		if ( array_key_exists ( $method ,  $paramDefineArray )) 
		{
			$actionParam = $paramDefineArray[$method ];
			foreach ($actionParam as $pName => $pType) 
			{
				switch ($pType)
				{
					case  "int" :
						$pValue=$http->getInt($pName);
						break;
					case  "bool" :
						$pValue=$http->getBool($pName);
						break;
					case  "float" :
						$pValue=$http->getFloat($pName);
						break;
					case  "json" :
						$pValue=$http->getJsonObj($pName);
                        //$this->logger->warn( __LINE__ .' '. __FUNCTION__ .' '.$pName.'值'.var_export($pValue,true)); 
						break;
					default:
						$pValue=$http->getString($pName);
				}
				//增加此修改是为了支持action函数采用默认值，使用时应注意：一旦开始使用默认值，后续的参数都不应该再传
				//该修改是个双刃剑，容易引起其他混乱，在开发初期还是不易使用。
				//if($pValue!=null)
				{
					array_push($parray,$pValue);  
				}
			}
		}
        

        return call_user_func_array(array($this, $method), $parray); 
    }
    
    /**
     * ACL数据规则关键字过滤，不允许出现{}[]等权限系统使用关键字.
     * @param string $dataRules 数据规则字符串
     * @return string 过滤后的数据规则字符串
     * @throws 
     */    
    public function aclDataRulesFilter($dataRules){
        $change = array('{','}','[',']');
        $ret = str_replace($change,'',$dataRules);
        return $ret; 
    }    
    
}

?>