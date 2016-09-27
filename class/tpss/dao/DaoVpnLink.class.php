<?php

namespace dao;
 

/**
 * 
 * @author Therfaint-
 *
 */
 
class DaoVpnLink{
	
    private $logger;                   //日志对象
    private $pdodb;					   //pdo数据库对象
    
    public function __construct(){
        $this->logger = \Logger::getLogger( __CLASS__ );
		$this->db = \db\PdoDB::getInstance();
    }    
    
    
    /**
     * 新增一条链路
     */    
    public function addNewLink($vpnservid, $linkid, $startnodeid, $endnodeid, $delaytime)
    {  
		$sql =<<<SQL
			INSERT INTO t_vpn_link (vpn_servid,vpn_link_id,start_node_id,
				end_node_id,delay_time)
			VALUES
			(:vpn_servid,:vpn_link_id,:start_node_id,:end_node_id,:delay_time);
SQL;
		$parray = array(
			":vpn_servid"=>$vpnservid,
			":vpn_link_id"=>$linkid,
			":start_node_id"=>$startnodeid,
			":end_node_id"=>$endnodeid,
			":delay_time"=>$delaytime
			);

		$result = $this->pdodb->execute($sql,$parray);
		
    	if($result){
            return json_encode(array("success"=>true));
        }else{
            return json_encode(array("msg"=>"error"));
        }

		//有个别情况下如果主键是自增序列，则用如下语句获得最后的插入ID
        //$sql = "SELECT LAST_INSERT_ID();";
        //$id = $this->pdodb->get_var($sql);

    }
    /**
     * 通过servid和linkid查询记录
     */    
    public function getALink($servid, $linkid)
    {  
		$sql =<<<SQL
			SELECT vpn_servid,vpn_link_id,start_node_id,end_node_id,delay_time 
			FROM t_vpn_link WHERE vpn_servid = :vpn_servid AND vpn_link_id = :vpn_link_id ;
SQL;
		$parray = array(
			":vpn_servid"=>$servid,
			":vpn_link_id"=>$linkid
			);

		$result = $this->db->get_results($sql,"OBJECT",$parray);
		
        return $result;
    }
    
    /**
     * 通过servid查询所有link记录
     */
    public function getAllLink($servid)
    {
    	$sql =<<<EOF
    		SELECT vpn_servid,vpn_link_id,start_node_id,end_node_id,delay_time 
			FROM t_vpn_link WHERE vpn_servid = :vpn_servid;
EOF;
    	$parray = array(
    			":vpn_servid"=>$servid
    	);
    	
    	$result = $this->db->get_results($sql,"OBJECT",$parray);
    	
    	return $result;
    	
    }
    
    /**
     * 通过servid和linkid删除该条记录
     */    
    public function delLink($servid, $linkid)
	{  
		//删除操作，一般建议通过标记为设置做逻辑上的操作，不建议直接删除记录
		$sql =<<<SQL
			DELET FROM t_vpn_link
			    WHERE vpn_servid = :vpn_servid AND vpn_link_id = :vpn_link_id ;
SQL;
		$parray = array(
			":vpn_servid"=>$servid,
			":vpn_link_id"=>$linkid
			);

		$result = $this->db->execute($sql,$parray); 
		
		if($result){
			return json_encode(array("success"=>"true"));
		}else{
			return json_encode(array("msg"=>"error"));
		}
	}
    
    
    /**
     * 更新Link信息
     */    
    public function updateLink($vpnservid, $linkid, $startnodeid, $endnodeid, $delaytime)
    {
		$sql =<<<SQL
			UPDATE t_vpn_link SET vpn_servid = :vpn_servid,vpn_link_id = :vpn_link_id,
				start_node_id = :start_node_id,end_node_id = :end_node_id,delay_time = :delay_time
			    WHERE vpn_servid = :vpn_servid AND vpn_link_id = :vpn_link_id ;
SQL;
		$parray = array(
			":vpn_servid"=>$vpnservid,
			":vpn_link_id"=>$linkid,
			":start_node_id"=>$startnodeid,
			":end_node_id"=>$endnodeid,
			":delay_time"=>$delaytime
			);
		
		$result = $this->db->execute($sql,$parray);
		
		if($result){
			return json_encode(array("success"=>true));
		}
		else{
			return json_encode(array("msg"=>"error"));
		}
    }
}

?>