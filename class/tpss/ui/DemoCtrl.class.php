<?php
/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.dimpt.com/
 */
namespace ui;

/**
 * 前台页面中用到的各种树状选择框的数据生成类
 *
 * @author songqj
 * @since 1.0
 */
class DemoCtrl extends \framework\Controller
{
    
    public function __construct(){
    }

    public function getParamInfoInterface()
    {
		$actionParam=array(
			"demoAction"=>array("name"=>"string","hellomsg"=>"string"),
			"addRecAction"=>array("keyfield"=>"string","field1"=>"string","field2"=>"string"),
			"queryRecAction"=>array("keyfield"=>"string")
		);
		return $actionParam;

    }

    //样例控制接口
    public function demoAction($name,$hellomsg)
	{

		$logic = new \comm\DemoLogic();
		$jsonstr = $logic->demo($name,$hellomsg);

		return $jsonstr;
    }    

    //添加数据记录的接口
    public function addRecAction($keyField,$field1,$field2)
	{

		$dao = new \dao\DaoDemo();
		$jsonstr = $dao->addDemoObj($keyField,$field1,$field2);

		return $jsonstr;
    }    

    //查询记录的接口
    public function queryRecAction($keyField,$field1,$field2)
	{

		$dao = new \dao\DaoDemo();
		$jsonstr = $dao->queryDemoObj($keyField);
		return $jsonstr;
    }    

}
?>