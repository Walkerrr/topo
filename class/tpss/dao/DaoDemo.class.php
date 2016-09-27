<?php
/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.dimpt.com/ 
 */
namespace dao;

/**
 * 数据库访问封装类样例
 * 每个dao应包含基本的四个操作，新增记录，删除记录，更新记录和查询记录
 * 对于更新操作可以细分为多个操作，对于查询操作根据业务需求可以灵活扩充
 * @property 
 *
 * @author songqj
 * @since 1.0
 * 
 */
class DaoDemo{
    private $logger;                   //日志对象
    private $pdodb;					   //pdo数据库对象
    
    public function __construct(){
        $this->logger = \Logger::getLogger( __CLASS__ );
		$this->db = \db\PdoDB::getInstance();
    }    
    
    
    /**
     * 新增对象.
     */    
    public function addDemoObj($keyField,$field1,$field2)
    {  
		$sql =<<<SQL
			INSERT INTO t_demo (keyfield,field1,field2)
			VALUES
			(:KeyField,:Field1,:Field2);
SQL;
		$parray = array(
			":KeyField"=>$keyField,
			":Field1"=>$field1,
			":Field2"=>$field2
			);

		$result = $this->pdodb->execute($sql,$parray);
        if($result){
    		return true;
        }
        else{
            return false;
        }

		//有个别情况下如果主键是自增序列，则用如下语句获得最后的插入ID
        //$sql = "SELECT LAST_INSERT_ID();";
        //$id = $this->pdodb->get_var($sql);

    }
    /**
     * 查询对象
     */    
    public function queryDemoObj($keyField)
    {  
		$sql =<<<SQL
			SELECT keyfield,field1,field2 FROM t_demo
			WHERE keyfield = :KeyField ;
SQL;
		$parray = array(
			":KeyField"=>$keyField
			);

		$result = $this->db->get_results($sql,"OBJECT",$parray);
        return $result;
    }
    
    /**
     * 删除对象
     */    
    public function deleteDemoObj($planInfo)
	{  
		//删除操作，一般建议通过标记为设置做逻辑上的操作，不建议直接删除记录
		$sql =<<<SQL
			UPDATE t_demo SET deleteflag = "1" 
			OR
			DELET FROM t_demo
			    WHERE keyfield = :KeyField ;
SQL;
		$parray = array(
			":KeyField"=>$keyField
			);

		$this->db->execute($sql,$parray); 
	}
    
    
    /**
     * 更新对象,一般情况下可以支持更新不存在时自动插入新记录
     */    
    public function updateDemoObj($keyField,$field1,$field2)
    {
		$sql =<<<SQL
			UPDATE t_demo SET field1 = :Field1,field2 = :Field2 
			    WHERE keyfield = :KeyField ;
SQL;
		$parray = array(
			":KeyField"=>$keyField,
			":Field1"=>$field1,
			":Field2"=>$field2
			);
		$this->db->execute($sql,$parray); 
    }
        
}

?>