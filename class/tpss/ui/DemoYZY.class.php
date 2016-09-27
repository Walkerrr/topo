<?php
/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.dimpt.com/
 */
namespace ui;

/**
 * 样例类
 *
 * @author songqj
 * @since 1.0
 */
class DemoYZY extends \framework\Controller
{
    
    public function __construct(){
    }

    public function getParamInfoInterface()
    {
		$actionParam=array(
			"abcAction"=>array("company"=>"string")
		);
		return $actionParam;

    }

    //样例控制接口
    public function abcAction($name)
	{

		//$logic = new \comm\DemoLogic();
		//$jsonstr = $logic->demo($name,$hellomsg);
        $jsonstr="我曾经工作在".$name."公司";

		return $jsonstr;
    }    


}
?>