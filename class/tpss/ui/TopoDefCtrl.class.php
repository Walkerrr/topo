<?php

namespace ui;

/**
 *
 * @author Therfaint-
 *        
 */
class TopoDefCtrl extends \framework\Controller
{

    private $topo;

    public function __construct()
    {}

    public function getParamInfoInterface()
    {
        $actionParam = array(
            "addTopoDefAction" => array(
                "topo_type" => "string",
                "topo_level" => "string",
            	"descr" => "string",
            	"nodeicontype" => "string",
            	"linklinetype" => "string",
            	"nodeconvtype" => "string",
            	"linkconvtype" => "string"
            ),
            "delTopoDefAction" => array(
                "topo_type" => "string",
                "topo_level" => "string"
            ),
        	"updateTopoDefAction" => array(
        		"topo_type" => "string",
        		"topo_level" => "string",
        		"descr" => "string",
        		"nodeicontype" => "string",
        		"linklinetype" => "string",
        		"nodeconvtype" => "string",
        		"linkconvtype" => "string",
                     "topoType" => "string",
                     "topoLevel" => "string"
        	),
        	"getAllByTypeAction" => array(
        		"topo_type" => "string"	
        	),
        	"getAllByLevelAction" => array(
        		"topo_level" => "string"	
        	),
        	"getAllByTypeAndLevelAction" => array(
        		"topo_type" => "string",
        		"topo_level" => "string"
        	)
        );
        return $actionParam;
    }
    
    public function getAllByTypeAndLevelAction($topo_type,$topo_level)
    {
    	
    	/*if($topo_type == '基本拓扑'){
    		$topo_type = '0';
    	}*/
    	if($topo_level == '全国拓扑'){
    		$topo_level = '00';
    	}else if($topo_level == '省级拓扑'){
    		$topo_level = '01';
    	}else if($topo_level == '本地网拓扑'){
    		$topo_level = '02';
    	}
    	
    	$dao = new \dao\DaoTopoDef();
    	$jsonStr = $dao->getAllByTypeAndLevel($topo_type, $topo_level);
    	return $jsonStr;
    }
    
    public function getAllByTypeAction($topo_type)
    {
    	
    	/*if($topo_type == '基本拓扑'){
    		$topo_type = '0';
    	}*/
    	
    	$dao = new \dao\DaoTopoDef();
    	$jsonStr = $dao->getAllByType($topo_type);
    	return $jsonStr;
    }
    
    public function getAllByLevelAction($topo_level)
    {
    	
    	if($topo_level == '全国拓扑'){
    		$topo_level = '00';
    	}else if($topo_level == '省级拓扑'){
    		$topo_level = '01';
    	}else if($topo_level == '本地网拓扑'){
    		$topo_level = '02';
    	}
    	
    	$dao = new \dao\DaoTopoDef();
    	$jsonStr = $dao->getAllByLevel($topo_level);
    	return $jsonStr;
    }

    public function getAllTopoDefForMainAction()
    {
        $dao = new \dao\DaoTopoDef();
        $jsonStr = $dao->getAllTopoDefForMain();
        return $jsonStr;
    }
    
    public function getAllTopoDefAction()
    {
    	$dao = new \dao\DaoTopoDef();
    	$jsonStr = $dao->getAllTopoDef();
    	return $jsonStr;
    }
    
    public function getAllTypeAction()
    {
    	$dao = new \dao\DaoTopoDef();
    	$jsonStr = $dao->getAllType();
    	return $jsonStr;
    }
    
    public function getAllLevelAction()
    {
    	$dao = new \dao\DaoTopoDef();
    	$jsonStr = $dao->getAllLevel();
    	return $jsonStr;
    }

    public function addTopoDefAction($topo_type,$topo_level,$descr,$nodeicontype,$linklinetype,$nodeconvtype,$linkconvtype)
    {
        if($topo_level == '全国拓扑'){
            $topo_level = '00';
        }else if($topo_level == '省级拓扑'){
            $topo_level = '01';
        }else if($topo_level == '本地网拓扑'){
            $topo_level = '02';
        }

        if($nodeicontype == '示例图标'){
            $nodeicontype = '0';
        }else if($nodeicontype == '厂商图标'){
            $nodeicontype = '1';
        }

        if($linklinetype == '链路接口'){
            $linklinetype = '0';
        }else if($linklinetype == '链路带宽'){
            $linklinetype = '1';
        }

        if($nodeconvtype == '物理节点'){
            $nodeconvtype = '0';
        }else if($nodeconvtype == '逻辑节点'){
            $nodeconvtype = '1';
        }

        if($linkconvtype == '物理链路'){
            $linkconvtype = '0';
        }else if($linkconvtype == '聚合显示'){
            $linkconvtype = '1';
        }

        $dao = new \dao\DaoTopoDef();
        $jsonStr = $dao->addTopoDef($topo_type,$topo_level,$descr,$nodeicontype,$linklinetype,$nodeconvtype,$linkconvtype);
        return $jsonStr;
    }

    public function delTopoDefAction($topo_type, $topo_level)
    {
        if($topo_level == '全国拓扑'){
            $topo_level = '00';
        }else if($topo_level == '省级拓扑'){
            $topo_level = '01';
        }else if($topo_level == '本地网拓扑'){
            $topo_level = '02';
        }
        $dao = new \dao\DaoTopoDef();
        $jsonStr = $dao->delTopoDef($topo_type, $topo_level);
        return $jsonStr;
    }

    public function updateTopoDefAction($topo_type,$topo_level,$descr,$nodeicontype,$linklinetype,$nodeconvtype,$linkconvtype,$topoType,$topoLevel)
    {
        if($topo_level == '全国拓扑'){
            $topo_level = '00';
        }else if($topo_level == '省级拓扑'){
            $topo_level = '01';
        }else if($topo_level == '本地网拓扑'){
            $topo_level = '02';
        }
        if($nodeicontype == '示例图标'){
            $nodeicontype = '0';
        }else if($nodeicontype == '厂商图标'){
            $nodeicontype = '1';
        }

        if($linklinetype == '链路接口'){
            $linklinetype = '0';
        }else if($linklinetype == '链路带宽'){
            $linklinetype = '1';
        }

        if($nodeconvtype == '物理节点'){
            $nodeconvtype = '0';
        }else if($nodeconvtype == '逻辑节点'){
            $nodeconvtype = '1';
        }

        if($linkconvtype == '物理链路'){
            $linkconvtype = '0';
        }else if($linkconvtype == '聚合显示'){
            $linkconvtype = '1';
        }

        if($topoLevel == '全国拓扑'){
            $topoLevel = '00';
        }else if($topoLevel == '省级拓扑'){
            $topoLevel = '01';
        }else if($topoLevel == '本地网拓扑'){
            $topoLevel = '02';
        }
        
        $dao = new \dao\DaoTopoDef();
        $jsonStr = $dao->updateTopoDef($topo_type, $topo_level, $descr, $nodeicontype, $linklinetype, $nodeconvtype, $linkconvtype,$topoType,$topoLevel);
        return $jsonStr;
    }
    
    
}