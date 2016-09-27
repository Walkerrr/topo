<?php
namespace dao;

/**
 *
 * @author Therfaint-
 *        
 */
class DaoTopoDef
{

    private $logger;
 // 日志对象
    private $pdodb;
 // pdo数据库对象
    public function __construct()
    {
        $this->logger = \Logger::getLogger(__CLASS__);
        $this->db = \db\PdoDB::getInstance();
    }

    
    /*
        ":topo_type"=>$topo_type, 拓扑分类：目前仅考虑基本拓扑，取0
    	":topo_level"=>$topo_level, 拓扑级别：00-全国拓扑 01-省级拓扑 02-本地网拓扑
    	":descr"=>$descr, 拓扑描述
    	":nodeicontype"=>$nodeicontype, 图标显示类型：0-示例图标 1-厂商图标
    	":linklinetype"=>$linklinetype, 链路显示类型：0-链路接口 1-链路带宽
    	":nodeconvtype"=>$nodeconvtype, 节点聚合类型：0-物理节点 1-逻辑节点
    	":linkconvtype"=>$linkconvtype  链路聚合类型：0-物理链路 1-聚合显示
     */
    
    /**
     * 新增
     */
    
    public function addTopoDef($topo_type,$topo_level,$descr,$nodeicontype,$linklinetype,$nodeconvtype,$linkconvtype){

          $selectSql = "SELECT * FROM t_topo_def WHERE topo_type = :topo_type AND topo_level = :topo_level ";

          $selectArray  = array(
                    ':topo_type' =>$topo_type,
                    ':topo_level'=> $topo_level
           );

          $selectResult = $this->db->get_results($selectSql, "OBJECT", $selectArray);

          if($selectResult){
              return json_encode(array(
                            "msg" => "exist"
                        ));
          }else{
                    $sql = "INSERT INTO t_topo_def (topo_type,topo_level,descr,nodeicontype,linklinetype,nodeconvtype,linkconvtype)
                VALUES (:topo_type,:topo_level,:descr,:nodeicontype,:linklinetype,:nodeconvtype,:linkconvtype)";
        
                    $parray = array(
                            ":topo_type"=>$topo_type,
                            ":topo_level"=>$topo_level,
                            ":descr"=>$descr,
                            ":nodeicontype"=>$nodeicontype,
                            ":linklinetype"=>$linklinetype,
                            ":nodeconvtype"=>$nodeconvtype,
                            ":linkconvtype"=>$linkconvtype
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
    	
    }

    public function getAllTopoDefForMain()
    {
        $sql = "SELECT * FROM t_topo_def";
        
        $result = $this->db->get_results($sql, "OBJECT", null);
       
        $output = (array)$result;
        $out = array();
         
        for($i=0;$i<count($output);$i++){
        
            $row = $output[$i];
            $row=(array)$row;
            /*
            if($row['topo_type'] == '0'){
                $row['topo_type'] = '基本拓扑';
            }*/
            
            if($row['topo_level'] == '00'){
                $row['topo_level'] = '全国拓扑';
            }else if($row['topo_level'] == '01'){
                $row['topo_level'] = '省级拓扑';
            }else if($row['topo_level'] == '02'){
                $row['topo_level'] = "本地网拓扑";
            }

            if($row['nodeicontype'] == '0'){
                $row['nodeicontype'] = '示例图标';
            }else if($row['nodeicontype'] == '1'){
                $row['nodeicontype'] = '厂商图标';
            }

            if($row['linklinetype'] == '0'){
                $row['linklinetype'] = '链路接口';
            }else if($row['linklinetype'] == '1'){
                $row['linklinetype'] = '链路带宽';
            }

            if($row['nodeconvtype'] == '0'){
                $row['nodeconvtype'] = '物理节点';
            }else if($row['nodeconvtype'] == '1'){
                $row['nodeconvtype'] = '逻辑节点';
            }

            if($row['linkconvtype'] == '0'){
                $row['linkconvtype'] = '物理链路';
            }else if($row['linkconvtype'] == '1'){
                $row['linkconvtype'] = '聚合显示';
            }

            array_push($out,$row);
        
        }
        
        return json_encode($out);
    }
    
    /**
     * 查询所有记录
     */
    public function getAllTopoDef()
    {
        $sql = "SELECT * FROM t_topo_def";
        
        $result = $this->db->get_results($sql, "OBJECT", null);
       
        $output = (array)$result;
    	$out = array();
    	 
    	for($i=0;$i<count($output);$i++){
    	
    		$row = $output[$i];
    		$row=(array)$row;
                    /*
    		
    		if($row['topo_type'] == '0'){
    			$row['topo_type'] = '基本拓扑';
    		}*/
    		
    		if($row['topo_level'] == '00'){
    			$row['topo_level'] = '全国拓扑';
    		}else if($row['topo_level'] == '01'){
    			$row['topo_level'] = '省级拓扑';
    		}else if($row['topo_level'] == '02'){
    			$row['topo_level'] = "本地网拓扑";
    		}
    		array_push($out,$row);
    	
    	}
    	
    	return json_encode($out);
    }
    
    /**
     * 查询所有级别
     */
    public function getAllLevel()
    {
    	$sql = "SELECT topo_level FROM t_topo_def";
    	
    	$result = $this->db->get_results($sql, "OBJECT", null);
    	
    	$output = (array)$result;
    	$out=array();
    	
    	for($i=0;$i<count($output);$i++){
    		
    		$row = $output[$i];
    		$row=(array)$row;
    		if($row['topo_level'] == '00'){
    			$row['topo_level'] = '全国拓扑';
    		}else if($row['topo_level'] == '01'){
    			$row['topo_level'] = '省级拓扑';
    		}else if($row['topo_level'] == '02'){
    			$row['topo_level'] = "本地网拓扑";
    		}
    		$row['id'] = $i;
    		array_push($out,$row);
    		
    	}
    
    	return json_encode($out);
    }
    
    /**
     * 查询所有类型
     */
    public function getAllType()
    {
    	$state0 = true;
    	
    	$sql = "SELECT topo_type,descr FROM t_topo_def GROUP BY topo_type";
    	 
    	$result = $this->db->get_results($sql, "OBJECT", null);
    	
    	/*$output = (array)$result;
    	$out = array();*/
    	
    	/*for($i=0;$i<count($output);$i++){
    		
    		$row = $output[$i];
    		$row=(array)$row;
    		if($row['topo_type'] == '0'){
    			if($state0){
	    			$row['topo_type'] = '基本拓扑';
	    			$state0 = false;
    			}else{
    				continue;
    			}
    		}
    		$row['id'] = $i;
    		array_push($out,$row);
    		
    	}*/
    
    	/*return json_encode($out);*/
           return json_encode($result);
    }
    
    /**
     * 通过类型查找
     */
    public function getAllByType($topo_type)
    {
    	$sql = "SELECT * FROM t_topo_def WHERE topo_type = :topo_type";
    	
    	$parray = array(
    		":topo_type" => $topo_type	
    	);
    	
    	$result = $this->db->get_results($sql, "OBJECT", $parray);
    	
   	    $output = (array)$result;
    	$out = array();
    	
    	for($i=0;$i<count($output);$i++){
    		
    		$row = $output[$i];
    		$row=(array)$row;
    		/*if($row['topo_type'] == '0'){
    			$row['topo_type'] = '基本拓扑';
    		}*/
    		if($row['topo_level'] == '00'){
    			$row['topo_level'] = '全国拓扑';
    		}else if($row['topo_level'] == '01'){
    			$row['topo_level'] = '省级拓扑';
    		}else if($row['topo_level'] == '02'){
    			$row['topo_level'] = "本地网拓扑";
    		}
    		array_push($out,$row);
    		
    	}
    	
    	return json_encode($out);
    }
    
    /**
     * 通过级别查找
     */
    public function getAllByLevel($topo_level)
    {
    	$sql = "SELECT * FROM t_topo_def WHERE topo_level = :topo_level";
    	 
    	$parray = array(
    			":topo_level" => $topo_level
    	);
    	 
    	$result = $this->db->get_results($sql, "OBJECT", $parray);
    	
    	$output = (array)$result;
    	$out = array();
    	 
    	for($i=0;$i<count($output);$i++){
    	
    		$row = $output[$i];
    		$row=(array)$row;
    		/*
    		if($row['topo_type'] == '0'){
    			$row['topo_type'] = '基本拓扑';
    		}*/
    		
    		if($row['topo_level'] == '00'){
    			$row['topo_level'] = '全国拓扑';
    		}else if($row['topo_level'] == '01'){
    			$row['topo_level'] = '省级拓扑';
    		}else if($row['topo_level'] == '02'){
    			$row['topo_level'] = "本地网拓扑";
    		}
    		array_push($out,$row);
    	
    	}
    	
    	return json_encode($out);
    }
    
    /**
     * 通过topo_type和topo_level查询记录
     */
    public function getAllByTypeAndLevel($topo_type,$topo_level)
    {
    	$sql = "SELECT * FROM t_topo_def WHERE topo_level = :topo_level AND topo_type = :topo_type";
    
    	$parray = array(
    			":topo_level" => $topo_level,
    			":topo_type" => $topo_type
    	);
    
    	$result = $this->db->get_results($sql, "OBJECT", $parray);
    	 
    	$output = (array)$result;
    	$out = array();
    
    	for($i=0;$i<count($output);$i++){
    		 
    		$row = $output[$i];
    		$row=(array)$row;
                    /*
    		if($row['topo_type'] == '0'){
    			$row['topo_type'] = '基本拓扑';
    		}*/
    
    		if($row['topo_level'] == '00'){
    			$row['topo_level'] = '全国拓扑';
    		}else if($row['topo_level'] == '01'){
    			$row['topo_level'] = '省级拓扑';
    		}else if($row['topo_level'] == '02'){
    			$row['topo_level'] = "本地网拓扑";
    		}
    		array_push($out,$row);
    		 
    	}
    	 
    	return json_encode($out);
    }

    /**
     * 通过topo_type和topo_level删除该条记录
     */
    public function delTopoDef($topo_type, $topo_level)
    {
        // 删除操作，一般建议通过标记为设置做逻辑上的操作，不建议直接删除记录
        $sql = "DELETE FROM t_topo_def WHERE topo_type = :topo_type AND topo_level = :topo_level";
        
        $parray = array(
            ":topo_type" => $topo_type,
            ":topo_level" => $topo_level
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
     * 更新信息
     */
    public function updateTopoDef($topo_type,$topo_level,$descr,$nodeicontype,$linklinetype,$nodeconvtype,$linkconvtype,$topoType,$topoLevel)
    {

        if($topoType == $topo_type && $topoLevel == $topo_level){

                 $sql = "UPDATE t_topo_def SET topo_type = :topo_type,topo_level = :topo_level,descr = :descr,nodeicontype = :nodeicontype,
                        linklinetype = :linklinetype,nodeconvtype = :nodeconvtype,linkconvtype = :linkconvtype 
                        WHERE topo_type = :topoType AND topo_level = :topoLevel ";

                $parray = array(
                    ":topo_type" => $topo_type,
                    ":topo_level" => $topo_level,
                    ":descr" => $descr,
                    ":nodeicontype" => $nodeicontype,
                    ":linklinetype" => $linklinetype,
                    ":nodeconvtype" => $nodeconvtype,
                    ":linkconvtype" => $linkconvtype,
                    "topoType" => $topoType,
                    ":topoLevel" => $topoLevel
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
        }else{
                $selectSql = "SELECT * FROM t_topo_def WHERE topo_type = :topo_type AND topo_level = :topo_level ";

          $selectArray  = array(
                    ':topo_type' =>$topo_type,
                    ':topo_level'=> $topo_level
           );

          $selectResult = $this->db->get_results($selectSql, "OBJECT", $selectArray);

          if($selectResult){
              return json_encode(array(
                            "msg" => "exist"
                        ));
          }else{
                $sql = "UPDATE t_topo_def SET topo_type = :topo_type,topo_level = :topo_level,descr = :descr,nodeicontype = :nodeicontype,
                        linklinetype = :linklinetype,nodeconvtype = :nodeconvtype,linkconvtype = :linkconvtype 
                        WHERE topo_type = :topoType AND topo_level = :topoLevel ";

                $parray = array(
                    ":topo_type" => $topo_type,
                    ":topo_level" => $topo_level,
                    ":descr" => $descr,
                    ":nodeicontype" => $nodeicontype,
                    ":linklinetype" => $linklinetype,
                    ":nodeconvtype" => $nodeconvtype,
                    ":linkconvtype" => $linkconvtype,
                    "topoType" => $topoType,
                    ":topoLevel" => $topoLevel
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
        }
    }
}

?>