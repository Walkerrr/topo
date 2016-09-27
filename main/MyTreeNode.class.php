<?php  
/**
 * @link http://www.citc.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.dimpt.com/
 */
namespace comm;

/**
 * 对生成树形控件节点数据的封装
 *
 *
 * @author songqj
 * @since 1.0
 * 
 * @change:2015-4-16 1.创建初始版本, songqj
 * @change:2015-6-15 1.对区域树进行了扩充，每次返回的节点包括省、本地网、汇聚区和综合业务点编码, songqj
 * @change:2015-11-6 1.在getQueryListTreeNode函数增加了集团层面查询分类'11'的支持。
 * @change:2016-2-17 1.在getQueryListTreeNode函数增加了多级树结构的支持。
 */
class TreeNode {  


    private $logger;                   //日志对象

    private $pdodb = null;              //数据库连接句柄
      
    /**
     * 构造函数.
     * @param 
     * @throws 
     */ 
    public function __construct() 
    {  
        $this->logger = \Logger::getLogger( __CLASS__ );
        $this->pdodb= \db\PdoDB::getInstance();	

    }  

    /**
     * 获取省、本地网、汇聚区的节点树，用于支持树控件
     * @param string $nodeID 父节点ID，默认为顶层节点
     * @return 指定节点下的子节点，以JSON格式返回
     */      
    public function getAreaTreeNode($nodeID="",$maxlevel=-1) 
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
    } 
	
      
    /**
     * 获取全国、省、本地网、汇聚区、综合业务区的指标定义树节点
     * @param string $nodeID 父节点ID，默认为顶层节点
     * @return 指定节点下的子节点，以JSON格式返回
     */      
    public function getKeyTreeNode($nodeID="",$level=-1) 
    {       
		$result= array();
		$param = array(":NodeID"=>$nodeID);

	    if(""==$nodeID && $level != -1&& $level != -9)
		{

			$result[0]['state']='closed';
			if(0 == $level)
			{
				$result[0]['keyid']='5000';
				$result[0]['keyname']='全国指标';
			}
			if(1==$level)
			{
			    $result[0]['keyid']='4000';
			    $result[0]['keyname']='省分指标';
			}
			if(2==$level)
			{
			    $result[0]['keyid']='3000';
			    $result[0]['keyname']='本地网指标';
			}
			if(3==$level)
			{
			    $result[0]['keyid']='2000';
			    $result[0]['keyname']='汇聚区指标';
			}
			if(4==$level)
			{
			    $result[0]['keyid']='1000';
			    $result[0]['keyname']='综合业务区指标';
			}
		}
		else
		{
			if(-9==$level)
			{
		        $result = $this->pdodb->get_results("select a.keyid as id,a.abbrname as text,"
			           ." if( (select count(1) from t_keydefine as b where b.parentid= a.keyid and  b.keytype='09' ),'closed','open') as state "
					   ." from t_keydefine a where a.keytype='09' and a.parentid=:NodeID order by keyid",'ARRAY',$param);
			}
			else
			{
		        $result = $this->pdodb->get_results("select keyid ,abbrname as keyname,"
			           ." if(keytype='09','closed','open') as state from t_keydefine where parentid=:NodeID  order by keyid",'ARRAY',$param);				
			}
			
		}
		
		return json_encode($result);

    } 
	

    /**
     * 获取省级树节点
     * @param string $nodeID 父节点ID，默认为顶层节点
     * @return 指定节点下的子节点，以JSON格式返回
     */      
    public function getProvinceTreeNode($userName="") 
    {       
		$result= array();
		$param = array();

		if(""==$userName)
		{
			$result = $this->pdodb->get_results("select provinceid as id,provincename as text,"
				   ." 'open' as state from t_province order by provinceid",'ARRAY');				
		}
		else	
		{
			$sql=<<<SQL
               SELECT a.provinceid as id,a.provincename as text, 'open' as state 
				 FROM t_province a, t_userRgn b
                 WHERE a.ProvinceID = b.RgnID and b.username=:UserName
                 ORDER by a.provinceid
SQL;
			$param = array(":UserName"=>$userName);
			$result = $this->pdodb->get_results($sql,'ARRAY',$param);			
		
		}
		
		return json_encode($result);

    }    

    /**
     * 获取省级和本地网树节点
     * @param string $nodeID 父节点ID，默认为顶层节点
     * @return 指定节点下的子节点，以JSON格式返回
     */      
    public function getProvCityTreeNode($userName="") 
    {       
		$result= array();
		$param = array();

		if(""==$userName)
		{
			$result = $this->pdodb->get_results("select provinceid as id,provincename as text,"
				   ." 'closed' as state from t_province order by provinceid",'ARRAY');				
		}
		else	
		{
			$sql=<<<SQL
               SELECT a.provinceid as id,a.provincename as text, 'closed' as state 
				 FROM t_province a, t_userRgn b
                 WHERE a.ProvinceID = b.RgnID and b.username=:UserName
                 ORDER by a.provinceid
SQL;
			$param = array(":UserName"=>$userName);
			$result = $this->pdodb->get_results($sql,'ARRAY',$param);			
		
		}

        //循环取出省分下的本地网编码
		$provSum = count($result);

		for($i=0;$i<$provSum;$i++)
		{
			$result[$i]["children"]=
				$this->pdodb->get_results("select cityid as id,cityname as text,"
				   ." 'open' as state from t_city where provinceid ='".$result[$i]["id"]."' order by cityid",'ARRAY');
		}

		$ret=json_encode($result);

        return $ret;
    }   
	



    /**
     * 获取基础数据管理的树状节点
     * @return 指定节点下的子节点，以JSON格式返回
     */      
    public function getDatTblListTreeNode($level="04") 
    {       
		$result= array();
		$param = array();

        $result[0] = array("id" => "00", "text" => "基础信息库");
        $result[1] = array("id" => "01", "text" => "现状信息库");
        $result[2] = array("id" => "02", "text" => "需求项目库");
        $result[3] = array("id" => "03", "text" => "业务需求表");

		$sql=<<<SQL
             SELECT tblsubtype as id,typename as text,tblname,cfgname 
			   FROM t_dattbllist
			   WHERE tbltype=:TblType
			   ORDER BY tblsubtype
SQL;

		$param = array(":TblType"=>"00");
		$result[0]["children"] = $this->pdodb->get_results($sql,'ARRAY',$param);			
		$param = array(":TblType"=>"01");
		$result[1]["children"] = $this->pdodb->get_results($sql,'ARRAY',$param);			
		$param = array(":TblType"=>"02");
		$result[2]["children"] = $this->pdodb->get_results($sql,'ARRAY',$param);			
		$param = array(":TblType"=>"03");
		$result[3]["children"] = $this->pdodb->get_results($sql,'ARRAY',$param);			
		
		$ret=json_encode($result);

        return $ret;
    }    


    /**
     * 获取定制查询的树状节点,已经修改支持多级树状目录
     * @return 指定节点下的子节点，以JSON格式返回
	 * 本功能最初设计时用于在数据管理模块显示，使用中根据使用人员的意见，要把显示的内容分开到网络分析
	 * 和数据管理两个模块，因此该模块根据数据显示的位置生成不同的树，表的数据结构和查询管理都支持多级
	 * 树状显示，但是在最终用户展现时，本函数仅支持一级展示。如果有需要时，通过对本函数的改造即可支持
	 * 多级树，前台代码无需做任何改动，目前已支持多级目录
	 * 注意：不在网络分析模块显示的自定义查询默认都要求是本地网级别,填入的级别无效。
     */      
    public function getQueryListTreeNode($level="02",$showlocation='NET') 
    {       
		$param = array();

        //用于区分显示在网络分析界面还是数据管理界面，对于网络分析界面，要求按级别显示查询，
		//注意目录和条目权限是同时限制的
        if($showlocation=='NET')
		{
			$sql=<<<SQL
				 SELECT queryid,queryname,querytype FROM t_querydefine
				   WHERE querytype='09' AND queryid IN ('02','04','06','08','11') AND usedscope=:Level AND (parentid='' OR parentid is null) 
				   ORDER BY showorder,queryid
SQL;
		    $result = $this->pdodb->get_results($sql,'ARRAY',array(":Level"=>$level));			

            //由于网络分析可以切换级别，因此这里必须严格限制定制查询的权限级别
		    $sql=<<<SQL
             SELECT queryid,queryname,querytype FROM t_querydefine
			   WHERE  parentid = :ParentID AND usedscope=:Level
			   ORDER BY showorder,queryid
SQL;


		}
		else
        //对于数据管理界面显示级别，查询的都是本地网的，因此无需级别限制
		{
			$sql=<<<SQL
				 SELECT queryid,queryname,querytype FROM t_querydefine
				   WHERE querytype='09' AND queryid NOT IN ('02','04','06','08','11') AND (parentid='' OR parentid is null)
				   ORDER BY showorder,queryid
SQL;
		    $result = $this->pdodb->get_results($sql,'ARRAY');			

		    $sql=<<<SQL
             SELECT queryid,queryname,querytype FROM t_querydefine
			   WHERE  parentid = :ParentID AND (TRUE OR usedscope=:Level)
			   ORDER BY showorder,queryid
SQL;

		}

        $nodeNum = count($result);
		for($i=0;$i<$nodeNum;$i++)
		{
			$param = array(":ParentID"=>$result[$i]["queryid"],":Level"=>$level);
		    //$result[$i]["children"] = $this->pdodb->get_results($sql,'ARRAY',$param);
			//以下代码替换上一行用来支持多级树状结构
            $children = $this->pdodb->get_results($sql,'ARRAY',$param);
			$childnum = count($children);

			for($m=0;$m<$childnum;$m++)
			{
				if($children[$m]["querytype"]=="09")
				{
					$nodes= $this->getFreeQueryChildNode($children[$m]["queryid"],$level,$showlocation);
					if(count($nodes)>0)
					{
						$children[$m]["children"]=$nodes;
					}
				}
			}
			$result[$i]["children"] = $children;

			
		}
		
		$ret=json_encode($result);

        return $ret;
    }    

    /**
     * 用来支持定制查询的多级树节点，与getQueryListTreeNode函数配合使用
	 * 对于网络分析界面，必须按界别限制，对于数据管理界面，不分级别，都是本地网级别的查询
     * @return 指定节点下的子节点，以JSON格式返回
     */      
    public function getFreeQueryChildNode($parentid,$level,$showlocation)
	{

         if($showlocation=='NET')
		{
			$sql = <<<SQL
				SELECT queryid,queryname,querytype FROM t_querydefine
				WHERE parentid=:ParentID AND usedscope=:Level
				ORDER BY showorder,queryid
SQL;
		}
		else
		{
			$sql = <<<SQL
				SELECT queryid,queryname,querytype FROM t_querydefine
				WHERE parentid=:ParentID AND (TRUE OR usedscope=:Level)
				ORDER BY showorder,queryid
SQL;
		}

		$params=array(":ParentID"=>$parentid,":Level"=>$level);
		$results=$this->pdodb->get_results($sql,"ARRAY",$params);
		$childnum=count($results);
		for($i=0;$i<$childnum;$i++)
		{
			if($results[$i]["querytype"]=="09")
			{
			    $nodes= $this->getFreeQueryChildNode($results[$i]["queryid"],$level,$showlocation);
                if(count($nodes)>0)
			    {
                    $results[$i]["children"]=$nodes;
			    }
			}

		}
		return $results;

	}

    /**
     * 获取预定义指标分析图表的树状节点
     * @return 指定节点下的子节点，以JSON格式返回
     */      
    public function getPreKeyChartTreeNode($level="02") 
    {       
		$result= array();
		$param = array();

        $result[0] = array("keychartid" => "1", "keychartname" => "资源现状分析");
        $result[1] = array("keychartid" => "2", "keychartname" => "投资造价分析");
        $result[2] = array("keychartid" => "3", "keychartname" => "规划方案分析");
        $result[3] = array("keychartid" => "4", "keychartname" => "规划效果分析");


		$sql=<<<SQL
             SELECT keychartid,keyid,keychartname,showtype,helptips 
			   FROM t_prekeychart 
			   WHERE level = :Level
			     AND keychartid like :KeyChartType
			   ORDER BY showorder,keychartid
SQL;
		$param[":Level"]=$level;

		$param[":KeyChartType"]="1%";
		$result[0]["children"] = $this->pdodb->get_results($sql,'ARRAY',$param);			
		$param[":KeyChartType"]="2%";
		$result[1]["children"] = $this->pdodb->get_results($sql,'ARRAY',$param);			
		$param[":KeyChartType"]="3%";
		$result[2]["children"] = $this->pdodb->get_results($sql,'ARRAY',$param);			
		$param[":KeyChartType"]="4%";
		$result[3]["children"] = $this->pdodb->get_results($sql,'ARRAY',$param);			
		
		$ret=json_encode($result);

        return $ret;
    }    

    /**
     * 获取预定义指标分析图表的树状节点
     * @return 指定节点下的子节点，以JSON格式返回
     */      
    public function getHMapTreeNode($level="02") 
    {       
		$result= array();
		$param = array();

        $result[0] = array("keychartid" => "1", "keychartname" => "资源现状分析");
        $result[1] = array("keychartid" => "2", "keychartname" => "投资造价分析");
        $result[2] = array("keychartid" => "3", "keychartname" => "规划方案分析");
        $result[3] = array("keychartid" => "4", "keychartname" => "规划效果分析");


		$sql=<<<SQL
             SELECT keychartid,keyid,keychartname,showtype,helptips,keylevel 
			   FROM t_prekeychart 
			   WHERE level = :Level AND ishmap = '1'
			     AND keychartid like :KeyChartType
			   ORDER BY keychartid
SQL;
		$param[":Level"]=$level;

		$param[":KeyChartType"]="1%";
		$result[0]["children"] = $this->pdodb->get_results($sql,'ARRAY',$param);			
		$param[":KeyChartType"]="2%";
		$result[1]["children"] = $this->pdodb->get_results($sql,'ARRAY',$param);			
		$param[":KeyChartType"]="3%";
		$result[2]["children"] = $this->pdodb->get_results($sql,'ARRAY',$param);			
		$param[":KeyChartType"]="4%";
		$result[3]["children"] = $this->pdodb->get_results($sql,'ARRAY',$param);			
		
		$ret=json_encode($result);

        return $ret;
    }    

}

?>