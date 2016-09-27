<?php

namespace dao;
 

/**
 * 
 * @author Therfaint-
 *
 */
 
class DaoCustomer{
	
    private $logger;                   //日志对象
    private $pdodb;					   //pdo数据库对象
    
    public function __construct(){
        $this->logger = \Logger::getLogger( __CLASS__ );
		$this->db = \db\PdoDB::getInstance();
    }    
    
    
    /**
     * 新增用户及其对应的vpn信息.
     */    
    public function addNewCustomerInfo($customerid, $customername, $vpnservid)
    {  
		$sql =<<<SQL
			INSERT INTO t_customer (customerid,customername,pri_vpnservid)
			VALUES
			(:customerid,:customername,:pri_vpnservid);
SQL;
		$parray = array(
			":customerid"=>$customerid,
			":customername"=>$customername,
			":pri_vpnservid"=>$vpnservid
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
     * 通过用户id查询属于他的vpnid
     */    
    public function getAllVpnByCustomerId($customerid)
    {  
		$sql =<<<SQL
			SELECT customerid,customername,pri_vpnservid FROM t_customer
			WHERE customerid = :customerid ;
SQL;
		$parray = array(
			":customerid"=>$customerid
			);

		$result = $this->db->get_results($sql,"OBJECT",$parray);
		
        return $result;
    }
    
    /**
     * 通过用户id删除该条记录
     */    
    public function delCustomerInfo($customerid)
	{  
		//删除操作，一般建议通过标记为设置做逻辑上的操作，不建议直接删除记录
		$sql =<<<SQL
			DELET FROM t_customer
			    WHERE customerid = :customerid ;
SQL;
		$parray = array(
			":customerid"=>$customerid
			);

		$result = $this->db->execute($sql,$parray); 
		
		if($result){
			return json_encode(array("success"=>"true"));
		}else{
			return json_encode(array("msg"=>"error"));
		}
	}
    
    
    /**
     * 更新用户信息
     */    
    public function updateCustomerInfo($customerid,$customername,$vpnservid)
    {
		$sql =<<<SQL
			UPDATE t_customer SET customerid = :customerid,
				customername = :customername, pri_vpnservid = :pri_vpnservid
			    WHERE customerid = :customerid ;
SQL;
		$parray = array(
			":customerid"=>$customerid,
			":customername"=>$customername,
			":pri_vpnservid"=>$vpnservid
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