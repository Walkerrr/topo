<?php

namespace ui;

/**
 *
 * @author Therfaint-
 *        
 */
class TopoNodeCtrl extends \framework\Controller
{

    private $toponode;

    public function __construct()
    {}

    public function getParamInfoInterface()
    {
        $actionParam = array(
        	"addTopoNodeAction" => array(
          	    "topo_type" => "string",
	    "topo_level" => "string",
 	    "nodes" => "array"
            ),
        	"getAllTopoNodesAction" => array(
        		"topo_type" => "string",
        		"topo_level" => "string",
                    "topo_id" => "string"
        	),
            "delTopoNodeAction" => array(
                "topo_type" => "string",
	      "topo_level" => "string",
                "topo_id" => "string",
                "nodes" => "array"
            ),
        	"updateTopoNodeAction" => array(
        		"topo_type" => "string",
    			"topo_id" => "string",
    			"topo_level" => "string",
    			"node_id" => "string",
    			"gis_x" => "string",
    			"gis_y" => "string",
    			"x" => "string",
    			"y" => "string",
    			"orderindex"=> "string"
        	),
        	"getAllAvailAction" => array(
        		"topo_type"=>"string",
        		"topo_level"=>"string",
        		"provinceid" => "string",
		"cityid" => "string",
                     "topo_id" => "string"
        	),
			"getNodesByProvinceIdAndCityIdAction" => array(
				"provinceid" => "string",
				"cityid" => "string"
			),
        	"getAllAvailWithoutCityIdAction" => array(
        		"topo_type"=>"string",
        		"topo_level"=>"string",
        		"provinceid" => "string",
                     "topo_id" => "string"
        	)
         /*"areaTreeAction"=>array("id"=>"string","maxlevel"=>"int")*/
        );
        return $actionParam;
    }
    
    public function getAllTopoNodesAction($topo_type, $topo_level,$topo_id)
    {
    	if($topo_type == '基本拓扑'){
    		$topo_type = '0';
    	}
    	if($topo_level == '全国拓扑'){
    		$topo_level = '00';
    	}else if($topo_level == '省级拓扑'){
    		$topo_level = '01';
    	}else if($topo_level == '本地网拓扑'){
    		$topo_level = '02';
    	}
    	
    	$dao = new \dao\DaoTopoNodes();
    	$jsonStr = $dao->getAllTopoNodes($topo_type, $topo_level,$topo_id);
    	return $jsonStr;
    }
    
    public function getAllAvailWithoutCityIdAction($topo_type,$topo_level,$provinceid,$topo_id)
    {
    	if($topo_type == '基本拓扑'){
    		$topo_type = '0';
    	}
    	if($topo_level == '全国拓扑'){
    		$topo_level = '00';
    	}else if($topo_level == '省级拓扑'){
    		$topo_level = '01';
    	}else if($topo_level == '本地网拓扑'){
    		$topo_level = '02';
    	}
    	$dao = new \dao\DaoTopoNodes();
    	$jsonStr = $dao->getAllAvailWithoutCityId($topo_type, $topo_level, $provinceid,$topo_id);
    	return $jsonStr;
    }
    
    public function initNodesAction()
    {
    	return 1;
    }

    public function getNodesByProvinceIdAndCityIdAction($provinceid,$cityid)
    {
    	if($topo_type == '基本拓扑'){
    		$topo_type = '0';
    	}
    	if($topo_level == '全国拓扑'){
    		$topo_level = '00';
    	}else if($topo_level == '省级拓扑'){
    		$topo_level = '01';
    	}else if($topo_level == '本地网拓扑'){
    		$topo_level = '02';
    	}
    	$dao = new \dao\DaoTopoNodes();
    	$jsonStr = $dao->getNodesByProvinceIdAndCityId($provinceid,$cityid);
    	return $jsonStr;
    }
    
    public function getAllAvailAction($topo_type,$topo_level,$provinceid,$cityid,$topo_id)
    {
    	if($topo_type == '基本拓扑'){
    		$topo_type = '0';
    	}
    	if($topo_level == '全国拓扑'){
    		$topo_level = '00';
    	}else if($topo_level == '省级拓扑'){
    		$topo_level = '01';
    	}else if($topo_level == '本地网拓扑'){
    		$topo_level = '02';
    	}
    	$dao = new \dao\DaoTopoNodes();
    	$jsonStr = $dao->getAllAvail($topo_type,$topo_level,$provinceid,$cityid,$topo_id);
    	return $jsonStr;
    }

    public function addTopoNodeAction($topo_type, $topo_level,$nodes)
    {
   	if($topo_type == '基本拓扑'){
    		$topo_type = '0';
    	}
    	if($topo_level == '全国拓扑'){
    		$topo_level = '00';
    	}else if($topo_level == '省级拓扑'){
    		$topo_level = '01';
    	}else if($topo_level == '本地网拓扑'){
    		$topo_level = '02';
    	}
        $dao = new \dao\DaoTopoNodes();
        $jsonStr = $dao->addTopoNode($topo_type, $topo_level,$nodes);
        return $jsonStr;
    }

    public function delTopoNodeAction($topo_type, $topo_level,$topo_id, $nodes)
    {
    	if($topo_type == '基本拓扑'){
    		$topo_type = '0';
    	}
    	if($topo_level == '全国拓扑'){
    		$topo_level = '00';
    	}else if($topo_level == '省级拓扑'){
    		$topo_level = '01';
    	}else if($topo_level == '本地网拓扑'){
    		$topo_level = '02';
    	}
        $dao = new \dao\DaoTopoNodes();
        $jsonStr = $dao->delTopoNode($topo_type, $topo_level,$topo_id, $nodes);
        return $jsonStr;
    }

    public function updateTopoNodeAction($topo_type, $topo_id, $topo_level, $node_id, $gis_x, $gis_y, $x, $y, $orderindex)
    {
        $dao = new \dao\DaoTopoNodes();
        $jsonStr = $dao->updateTopoDef($topo_type, $topo_id, $topo_level, $node_id, $gis_x, $gis_y, $x, $y, $orderindex);
        return $jsonStr;
    }

    /**
     * 获得区域树
     */     
    /*public function areaTreeAction($nodeid,$maxlevel)
    {
        $nodeTree = new \dao\DaoTopoNodes();
        $jsonstr = $nodeTree->getAreaTreeNode($nodeid,$maxlevel);
        return $jsonstr;

    }*/
    
    
}