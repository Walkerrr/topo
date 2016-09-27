<?php
namespace dao;

/**
 *
 * @author Therfaint-
 *        
 */
class DaoTopoNodes
{

    private $logger;
    private $pdodb;
    public function __construct()
    {
        $this->logger = \Logger::getLogger(__CLASS__);
        $this->db = \db\PdoDB::getInstance();
    }
    
    public function addTopoNode($topo_type, $topo_level, $nodes){

    	for($i=0;$i<count($nodes);$i++){
    		
    		$sql = "INSERT INTO t_topo_nodes (topo_type, topo_id, topo_level, node_id, gis_x, gis_y, x, y, orderindex)
    			VALUES (:topo_type, :topo_id, :topo_level, :node_id, :gis_x, :gis_y, :x, :y, :orderindex)";
    		
    		$parray = array(
    				":topo_type"=>$topo_type,
    				":topo_id"=>$nodes[$i]['topo_id'],
    				":topo_level"=>$topo_level,
    				":node_id"=>$nodes[$i][node_id],
    				":gis_x"=>$nodes[$i][gis_x],
    				":gis_y"=>$nodes[$i][gis_y],
    				":x"=>$nodes[$i][x],
    				":y"=>$nodes[$i][y],
    				":orderindex"=>$nodes[$i][orderindex]
    		);
    		
    		$result = $this->db->execute($sql, $parray);
    	}
    	
    	if ($result) {
    		return json_encode(array(
    				"success" => true
    		));
    	} else {
    		return json_encode(array(
    				"msg" => "error"
    		));
    	}
    	
    }
    
    public function initTopoNodes()
    {
    
    }
    
    public function getNodesByProvinceIdAndCityId($provinceid,$cityid)
    {
    	$sql = "SELECT * FROM t_route_node WHERE provinceid = :provinceid AND cityid = :cityid";
    
    	$parray = array(
    			":provinceid" => $provinceid,
    			":cityid" => $cityid
    	);
    
    	$result = $this->db->get_results($sql,"OBJECT",$parray);
    
    	return json_encode($result);
    }
    //00-CR路由器，10-RR路由器，20-BR路由器，21-外部BR路由器，30-AR接入路由器  40-UR用户路由器
    public function getAllAvail($topo_type,$topo_level,$provinceid,$cityid,$topo_id)
    {
    	$sql = "SELECT * FROM t_route_node WHERE node_id NOT IN 
    	(SELECT node_id FROM t_topo_nodes WHERE topo_level = :topo_level AND 
    	topo_type = :topo_type AND topo_id = :topo_id ) AND provinceid = :provinceid AND cityid = :cityid";
    
    	$parray = array(
    			":topo_level" => $topo_level,
    			":topo_type" => $topo_type,
    			":provinceid" => $provinceid,
    			":cityid" => $cityid,
                                ":topo_id" => $topo_id
    	);
    
    	$result = $this->db->get_results($sql,"OBJECT",$parray);
    
    	$output = (array)$result;
    	$out = array();
    	 
    	for($i=0;$i<count($output);$i++){
    	
    		$row = $output[$i];
    		$row=(array)$row;
    		
    		if($row['node_type'] == '00'){
    			$row['node_type'] = 'CR路由器';
    		}else if($row['node_type'] == '10'){
    			$row['node_type'] = 'RR路由器';
    		}else if($row['node_type'] == '20'){
    			$row['node_type'] = 'BR路由器';
    		}else if($row['node_type'] == '21'){
    			$row['node_type'] = "外部BR路由器";
    		}else if($row['node_type'] == '30'){
    			$row['node_type'] = "AR接入路由器";
    		}else if($row['node_type'] == '40'){
    			$row['node_type'] = "UR用户路由器";
    		}
    		array_push($out,$row);
    	
    	}
    	
    	return json_encode($out);
    }
    //00-CR路由器，10-RR路由器，20-BR路由器，21-外部BR路由器，30-AR接入路由器  40-UR用户路由器
    public function getAllAvailWithoutCityId($topo_type,$topo_level,$provinceid,$topo_id)
    {
    	$sql = "SELECT * FROM t_route_node WHERE node_id NOT IN
    	(SELECT node_id FROM t_topo_nodes WHERE topo_level = :topo_level AND
    	topo_type = :topo_type AND topo_id = :topo_id ) AND provinceid = :provinceid";
    
    	$parray = array(
    			":topo_level" => $topo_level,
    			":topo_type" => $topo_type,
    			":provinceid" => $provinceid,
                                ":topo_id" => $topo_id
    	);
    
    	$result = $this->db->get_results($sql,"OBJECT",$parray);
       
    	$output = (array)$result;
    	$out = array();
    	 
    	for($i=0;$i<count($output);$i++){
    	
    		$row = $output[$i];
    		$row=(array)$row;
    		
    		if($row['node_type'] == '00'){
    			$row['node_type'] = 'CR路由器';
    		}else if($row['node_type'] == '10'){
    			$row['node_type'] = 'RR路由器';
    		}else if($row['node_type'] == '20'){
    			$row['node_type'] = 'BR路由器';
    		}else if($row['node_type'] == '21'){
    			$row['node_type'] = "外部BR路由器";
    		}else if($row['node_type'] == '30'){
    			$row['node_type'] = "AR接入路由器";
    		}else if($row['node_type'] == '40'){
    			$row['node_type'] = "UR用户路由器";
    		}
    		array_push($out,$row);
    	
    	}
    	
    	return json_encode($out);
    }
    
    public function getAllTopoNodes($topo_type, $topo_level,$topo_id)
    {
        $sql = "SELECT a.node_name,a.node_id FROM t_route_node a WHERE a.node_id IN (SELECT c.node_id FROM t_topo_nodes c WHERE topo_type = :topo_type AND topo_level =:topo_level AND topo_id =:topo_id)";
        
        $parray = array(
        	":topo_type"=>$topo_type,
        	":topo_level"=>$topo_level,
          ":topo_id" => $topo_id
        );
        
        $result = $this->db->get_results($sql, "OBJECT", $parray);
       
        return json_encode($result);
    }

    
    public function delTopoNode($topo_type, $topo_level, $topo_id,$nodes)
    {
    	for($i=0;$i<count($nodes);$i++){
    		
	        $sql = "DELETE FROM t_topo_nodes WHERE topo_type = :topo_type AND topo_level = :topo_level AND topo_id = :topo_id AND node_id IN (:nodes)";
	        
	        $parray = array(
	        	":topo_type"=>$topo_type,
	        	":topo_level"=>$topo_level,
                    ":topo_id" => $topo_id,
	        	":nodes"=>$nodes[$i]
	        );
	        
	        $result = $this->db->execute($sql, $parray);
    	}
    	
        
        if ($result) {
            return json_encode(array(
                "success" => true
            ));
        } else {
            return json_encode(array(
                "msg" => "error"
            ));
        }
    }

    
    public function updateTopoDef($topo_type, $topo_id, $topo_level, $node_id, $gis_x, $gis_y, $x, $y, $orderindex)
    {
        $sql = "UPDATE t_topo_nodes SET topo_type = :topo_type,topo_id = :topo_id,topo_level = :topo_level,node_id = :node_id,
        		gis_x = :gis_x,gis_y = :gis_y,x = :x,y = :y,orderindex = :orderindex 
			    WHERE topo_type = :topo_type AND topo_id = :topo_id AND node_id = :node_id ";
        
        $parray = array(
    			":topo_type"=>$topo_type,
    			":topo_id"=>$topo_id,
    			":topo_level"=>$topo_level,
    			":node_id"=>$node_id,
    			":gis_x"=>$gis_x,
    			":gis_y"=>$gis_y,
    			":x"=>$x,
    			":y"=>$y,
    			":orderindex"=>$orderindex
    	);
        
        $result = $this->db->execute($sql, $parray);
        
        if ($result) {
            return json_encode(array(
                "success" => true
            ));
        } else {
            return json_encode(array(
                "msg" => "error"
            ));
        }
    }

     /**
     * 获取省、本地网、汇聚区的节点树，用于支持树控件
     * @param string $nodeID 父节点ID，默认为顶层节点
     * @return 指定节点下的子节点，以JSON格式返回
     */      
    /*public function getAreaTreeNode($nodeID="",$maxlevel=-1) 
    {   
        
        $nodeIDLen = strlen($nodeID);
        
        $param = array(":NodeID"=>$nodeID);
        $result= array();
        //返回全国根节点
        if(""==$nodeID)
        {
            $result[0]['state']='closed';
            $result[0]['id']='0';
            $result[0]['text']='全国';
            $result[0]['provinceid']='*';
            $result[0]['cityid']='*';
            $result[0]['conrgnid']='*';
            $result[0]['intesrvrgnid']='*';
        }
        //返回各省节点
        elseif("00"==$nodeID )
        {
            if(2==$maxlevel)
            {
                $result = $this->pdodb->get_results("select provinceid as id,"
                             ."provincename  as text,'open' as state,provinceid,'*' as cityid,"
                             ."'*' as conrgnid,'*' as intesrvrgnid  from t_province",'OBJECT');
            }
            else
            {
                $result = $this->pdodb->get_results("select provinceid as id,"
                             ."provincename as text,'closed' as state,provinceid,'*' as cityid,"
                             ."'*' as conrgnid,'*' as intesrvrgnid  from t_province",'OBJECT');
            }
        }
        //输出
        elseif("@"==substr($nodeID,0,1))
        {
            if(3==$nodeIDLen)
            {
                $result = $this->pdodb->get_results("select provinceid as id,"
                             ."provincename  as text,'closed' as state,provinceid,'*' as cityid,"
                             ."'*' as conrgnid,'*' as intesrvrgnid  from t_province where provinceid=:ProvinceID",
                                'OBJECT',array(":ProvinceID"=>substr($nodeID,1,2)));
            }
            elseif(7==$nodeIDLen)
            {
               $result = $this->pdodb->get_results("select cityid as id,cityname as text,"
                             ."provinceid,cityid,'*' as conrgnid,'*' as intesrvrgnid,"
                             ."'closed' as state from t_city where cityid=:CityID",
                                 'OBJECT',array(":CityID"=>substr($nodeID,1,6)) );
            }
            else
            {
                $result[0]['state']='open';
                $result[0]['id']='XXXXXXXXXXX';
                $result[0]['text']="未知的节点($nodeID)，请与系统维护员联系";
                $this->logger->warn( __LINE__ .' '. __FUNCTION__ .' '."在获取区域树节点时使用未知的节点编码($nodeID)"); 
            }
        }
        //返回各省内本地网节点，根据编码长度为2，且非‘00’判断
        elseif(2 ==$nodeIDLen)
        {
            if(3==$maxlevel)
            {
               $out = $this->pdodb->get_results("select cityid as id,cityname as text,"
                             ."provinceid,cityid,'*' as conrgnid,'*' as intesrvrgnid,"
                             ."'open' as state from t_city where provinceid=:NodeID",'OBJECT',$param );

                       $output = (array)$result;
                    $result = array();

                    $result[0]['state']='closed';
                $result[0]['id']='0';
                $result[0]['text']='全国拓扑';
                $result[0]['provinceid']='*';
                $result[0]['cityid']='*';
                $result[0]['conrgnid']='*';
                $result[0]['intesrvrgnid']='*';
                     
                    for($i=0;$i<count($output);$i++){
                    
                        $row = $output[$i];
                        $row=(array)$row;
                        
                        array_push($result,$row);
                    
                    }

            }
            else
            {
               $result = $this->pdodb->get_results("select cityid as id,cityname as text,"
                             ."provinceid,cityid,'*' as conrgnid,'*' as intesrvrgnid,"
                             ."'closed' as state from t_city where provinceid=:NodeID",'OBJECT',$param );
            }
        }
        //返回本地网内汇聚区节点，根据编码长度为6
        elseif(6 ==$nodeIDLen)
        {
            if(4==$maxlevel)
            {
               $result = $this->pdodb->get_results("select conrgnid as id,conrgnname as text,"
                             ."provinceid,cityid,conrgnid,'*' as intesrvrgnid,"
                             ."'open' as state from t_conregion where cityid=:NodeID",'ARRAY',$param);
            }
            else
            {
               $result = $this->pdodb->get_results("select conrgnid as id,conrgnname as text,"
                             ."provinceid,cityid,conrgnid,'*' as intesrvrgnid,"
                             ."'closed' as state from t_conregion where cityid=:NodeID",'ARRAY',$param);
            }
        }
        //返回汇聚区内综合业务接入区节点，根据编码长度为11判断
        elseif(11 ==$nodeIDLen)
        {
            $result = $this->pdodb->get_results("select intesrvrgnid as id,intesrvrgnname as text,"
                             ."provinceid,cityid,conrgnid,intesrvrgnid,"
                             ."'open' as state from t_intesrvregion where conrgnid=:NodeID",'ARRAY',$param);
        }
        else
        {
            $result[0]['state']='open';
            $result[0]['id']='XXXXXXXXXXX';
            $result[0]['text']="未知的节点($nodeID)，请与系统维护员联系";
            $this->logger->warn( __LINE__ .' '. __FUNCTION__ .' '."在获取区域树节点时使用未知的节点编码($nodeID)"); 
        }
        
        return json_encode($result);
    } */
}

?>