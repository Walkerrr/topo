<?php
/**
 * @link http://www.citc.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.citc.com/
 */
namespace ui;

/**
 * 前台页面中用到的各种树状选择框的数据生成类
 *
 * @author songqj
 * @since 1.0
 */
class CommTree extends \framework\Controller
{
    //private $user;
    
    public function __construct(){
        //$this->user = new \user\User();     
    }

    public function getParamInfoInterface()
    {
		$actionParam=array(
			"provTreeAction"=>array("username"=>"string"),
			"provCityTreeAction"=>array("username"=>"string"),
			"cityTreeAction"=>array("username"=>"string"),
			"cityRegionTreeAction"=>array("username"=>"string"),
			"areaTreeAction"=>array("id"=>"string","maxlevel"=>"int"),
			"datTblListTreeAction"=>array("level"=>"string"),
			"queryListTreeAction"=>array("level"=>"string","showlocation"=>"string"),
			"preKeyChartTreeAction"=>array("level"=>"string"),
			"preHMapTreeAction"=>array("level"=>"string"),
			"getKeyTreeAction"=>array("id"=>"string")

		);
		return $actionParam;

    }

    //获得省分区域树
    public function provTreeAction($username)
	{
		$jsonfile = G_ROOT_DIR."/treejson/prov_".$username.".json";
		if(fasle and file_exists($jsonfile))
		{
			$fh = fopen($jsonfile,"rb");
            $jsonstr = fread($fh, filesize($jsonfile));
            fclose($fh);
		}
		else
		{
			$nodeTree = new \comm\MyTreeNode();;
			$jsonstr = $nodeTree->getProvinceTreeNode($username);
			$fh = fopen($jsonfile,"wb");
			fwrite($fh,$jsonstr);
            fclose($fh);
		}
		return $jsonstr;
    }    

    /**
     * 获得省分及本地网区域树
     */     
    public function provCityTreeAction($username)
	{
		$nodeTree = new \comm\MyTreeNode();;
		$jsonstr = $nodeTree->getProvCityTreeNode($username);
		return $jsonstr;

    }

    /**
     * 获得区域树
     */     
    public function areaTreeAction($nodeid,$maxlevel)
	{
		$nodeTree = new \comm\MyTreeNode();;
		$jsonstr = $nodeTree->getAreaTreeNode($nodeid,$maxlevel);
		return $jsonstr;

    }
	

    /**
     * 获得指标树
     */     
    public function getKeyTreeAction($nodeid)
	{
		$nodeTree = new \comm\MyTreeNode();;
		$maxlevel = -9;
		if($nodeid==null)$nodeid="";
		$jsonstr = $nodeTree->getKeyTreeNode($nodeid,$maxlevel);
		return $jsonstr;

    }

    /**
     * 获得基础数据分类区域树
     */     
    public function datTblListTreeAction($level)
	{

		$jsonfile = G_ROOT_DIR."/treejson/datTbl_".$level.".json";
		if(false and file_exists($jsonfile))
		{
			$fh = fopen($jsonfile,"rb");
            $jsonstr = fread($fh, filesize($jsonfile));
            fclose($fh);
		}
		else
		{
			$nodeTree = new \comm\MyTreeNode();;
			$jsonstr = $nodeTree->getDatTblListTreeNode($level);
			$fh = fopen($jsonfile,"wb");
			fwrite($fh,$jsonstr);
            fclose($fh);
		}
		return $jsonstr;

    }


    /**
     * 获得定制查询导航树
     */     
    public function queryListTreeAction($level,$showlocation)
	{

		$jsonfile = G_ROOT_DIR."/treejson/datQry_".$level."_".$showlocation.".json";
		if(false and file_exists($jsonfile))
		{
			$fh = fopen($jsonfile,"rb");
            $jsonstr = fread($fh, filesize($jsonfile));
            fclose($fh);
		}
		else
		{
			$nodeTree = new \comm\MyTreeNode();;
			$jsonstr = $nodeTree->getQueryListTreeNode($level,$showlocation);
			$fh = fopen($jsonfile,"wb");
			fwrite($fh,$jsonstr);
            fclose($fh);
		}
		return $jsonstr;

    }

    /**
     * 获得预定义指标分析图表列表树
     */     
    public function preKeyChartTreeAction($level)
	{

		$jsonfile = G_ROOT_DIR."/treejson/preKeyChart_".$level.".json";
		if(false and file_exists($jsonfile))
		{
			$fh = fopen($jsonfile,"rb");
            $jsonstr = fread($fh, filesize($jsonfile));
            fclose($fh);
		}
		else
		{
			//$nodeTree = new \comm\MyTreeNode();;
			//$jsonstr = $nodeTree->getPreKeyChartTreeNode($level);
			$chardefine = new \comm\KeyChartDefine();
			$jsonstr = $chardefine->getKeyChartListTree($level,'0');
			$fh = fopen($jsonfile,"wb");
			fwrite($fh,$jsonstr);
            fclose($fh);
		}
		return $jsonstr;

    }

    /**
     * 获得预定义指标分析图表列表树
     */     
    public function preHMapTreeAction($level)
	{

		$jsonfile = G_ROOT_DIR."/treejson/preHMap_".$level.".json";
		if(false and file_exists($jsonfile))
		{
			$fh = fopen($jsonfile,"rb");
            $jsonstr = fread($fh, filesize($jsonfile));
            fclose($fh);
		}
		else
		{
			//$nodeTree = new \comm\MyTreeNode();;
			//$jsonstr = $nodeTree->getHMapTreeNode($level);
			$chardefine = new \comm\KeyChartDefine();
			$jsonstr = $chardefine->getKeyChartListTree($level,'1');
			
			$fh = fopen($jsonfile,"wb");
			fwrite($fh,$jsonstr);
            fclose($fh);
		}
		return $jsonstr;

    }

        
}
?>