<?php  
/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.dimpt.com/
 */
namespace db;

/**
 * 对PDO数据库的简单封装
 * 不建议使用传递拼接字符串的查询(测试除外)，应严格使用绑定参数用法。
 * 避免开发人员的大量修改，使用时只需要把现有代码的连接替换即可。 
 *
 *
 * @author lifq
 * @since 1.0
 * 
 * @change:2015-3-10 增加了两个函数,get_results和get_var, chenyf
 * @change:2015-4-14 1.所有类变量改为私有, songqj
 * @change:          2.构造函数支持多种数据库，且使用最新的全局变量, songqj
 * @change:          3.增加了日志记录，连接和执行错误都会记录日志, songqj
 * @change:          4.三个执行方法均支持？占位和命名绑定, songqj
 * @change:2015-4-24 1.解决了命名参数绑定的一个bug，关联数组迭代时需要用引用，问题 songqj
 * @change:                    情景很怪异，绑定一个参数不出错，绑定两个就出错 songqj
 * @change:2015-4-24 1.修正了get_var函数因为get_results增加绑定参数带来的bug，在调用时增加了OBJECT参数。songqj
 * @change:2015-4-30 1.修正了get_var函数查询不到记录时，未判断返回数组长度为0的一个bug。songqj
 * @change:2015-5-04 1.增加了绑定参数支持不同数据类型的功能，主要支持PARAM_STR和PARAM_INT，对于浮点数仍然采用PARAM_STR。songqj
 * @change:2015-5-06 1.修正了get_results在执行查询语句错误时仍然尝试获取记录的bug. songqj
 * @change:2015-5-07 1.移出文件末端多余的两个空格，两个空格会导致所有的输出到前台的都会多两个空格. songqj
 * @change:2015-5-22 1.修正绑定参数类型的一个错误，支持null类型. songqj
 * @change:2015-8-01 1.对类做了功能增强，支持对象直接新增或更新，并支持单值对象或列表的获取
                     2.利用PdoDB单实例的特征，增加一个表的列信息数组，在每个对象首次操作时储存对象的主键和属性列，有利于性能提升
					 3.获取单值对象时增加了主键校验，确保仅返回一条记录
 * @change:2016-4-08 1.增强getObjListFromDB函数功能，支持附加的SQL查询条件. 只要让查询条件的数组键值不为标志的字段即可 songqj
 * @change:2016-5-06 1.增强insertOrUpdateObj2DB函数功能，允许只做插入，对存在同样主键记录时返回错误 songqj

 */
class PdoDB {  


    private $logger;                   //日志对象

    private static $_instance = null;  //用于存放实例化的PdoDB对象 
    private $_dsn;                     //数据源字符串
    private $_user;                    //数据库用户名
    private $_pass;                    //数据库密码
    private $_charset;                 //数据库字符集
    private $_pdo = null;              //数据库连接句柄
	private $tblcolinfo;
      
    /**
     * 构造函数.
     * @param 
     * @return 本模块对应的名称
     * @throws 
     * @see connect() 
     */ 
    private function __construct() 
    {  
        $this->logger = \Logger::getLogger( __CLASS__ );
		$this->tblcolinfo = array();

        $this->_dsn = G_DB_TYPE.':host='.G_DB_SERVER.';port='.G_DB_PORT.';dbname='.G_DB_NAME;       
        $this->_dbuser = G_DB_USER;       
        $this->_dbpass = G_DB_PASSWORD;       
        $this->_charset = G_DB_CHARSET;
        $this->connect();
    }  

    /**
     * 数据库连接函数.
     * @return 数据库连接对象
     * @throws 
     */      
    private function connect() 
    {  
        if (null == $this->_pdo) {  
            try {  
                $this->_pdo = new \PDO($this->_dsn,$this->_dbuser,$this->_dbpass,
                                     array(\PDO::ATTR_PERSISTENT=>true,\PDO::MYSQL_ATTR_FOUND_ROWS =>true)); 

                //对mysql数据库的字符集设置指令
                if('mysql'==G_DB_TYPE)
                {
                    $this->_pdo->query('SET NAMES '.$this->_charset);       
                    //$this->_pdo->setAttribute(\PDO::MYSQL_ATTR_FOUND_ROWS,true);  
                }

                $this->_pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);  
            }
            catch (\PDOException $e) 
            { 
                $this->_pdo = null;
                $this->logger->error( __LINE__ .' '. __FUNCTION__ .' '.'数据库('.$this->_dsn.')连接失败'.$e->getMessage()); 
            }  
        }  
        return $this->_pdo;  
    } 
      
    /**
     * 返回当前对象实例，静态函数，保证只创建一个实例，只能通过该函数生成PdoDB对象，不能通过构造函数创建.
     * @return 唯一的PdoDB实例
     * @throws 
     */      
    public static function getInstance()       
    {       
        if (null == self::$_instance)       
        {       
            self::$_instance = new self();       
        }       

        return self::$_instance;       
    }       

    /**
     * 返回数据库连接实例.
     * @return 数据库连接实例
     * @throws 
     */      
     public function getConnect() 
    {  
        if (null == $this->_pdo)     
         {
                $this->connect();
         }
        return $this->_pdo;  
    } 

    /**
     * 执行SQL语句.
     * 注意：本方法支持用“？”做形参的参数绑定和用参数名做形参的绑定
     *     采用“?”做形参是，数组必须为索引数组数量；
     *     采用参数名作形参时，数组必须为关联数组。
     *     数组长度必须和形参数量匹配
     * @param string $_sql sql语句，可以带参数，所有参数采用?代替
     * @param array $_param sql参数值
     * @return 返回结果集
     * @throws 
     */         
    public function execute($_sql,$_param=null) 
    {  
        if(null == $this->_pdo)
            return null;
        if(strlen($_sql)>6)
		{
			if(false && substr_compare($_sql,"update",0,6,true)==0)
			{
				$sqllogger = \Logger::getLogger("updatesql");
				$sqllogger->debug("SQL语句为：".$_sql);
				$sqllogger->debug("参数为：".json_encode($_param));
			}
		}
        if(strlen($_sql)==0)
		{
			$tracearray = debug_backtrace(false);
			$traceLevel = count($tracearray);
			$cbtrace ="\n";
			for($i=0;$i<$traceLevel;$i++)
			{
				$cbtrace = $cbtrace .$i.":File->".$tracearray[$i]["file"].";LineNo->"
						 .$tracearray[$i]["line"].";Function->".$tracearray[$i]["function"].";\n";
			}
		    $this->logger->error( __LINE__ .' '. __FUNCTION__ .' '
                                     .'有代码传入空的SQL语句，参数为：'.json_encode($_param).'调用方：'.$cbtrace); 

		}

			
        $this->logger->trace( __LINE__ .' '. __FUNCTION__ .' SQL:('.$_sql.' )'); 
           
        try 
        {  

            $_stmt = $this->_pdo->prepare($_sql);  
            if(null != $_param && is_array($_param) )
            {

                //绑定"?"占位符的参数，通过判断是否有索引数组来确定
                if(isset($_param[0]))
                {

                    $arr = explode('?',$_sql);
                    $count = sizeof($arr)-1;
                    
                    for($i=1;$i<=sizeof($_param)&&$i<=$count;$i++)
                    {
                        $_stmt->bindParam($i,$_param[$i-1]);
                    }
                }
                else   //绑定用参数名占位的参数                
                {
                    //此处曾花费几乎半天的时间解决遇到的怪异问题，这里要用到引用。
                    foreach ($_param as $key => &$value) 
                    {
						
		                if(is_int($value))
						{
                            $paramType = \PDO::PARAM_INT;
						}
                        elseif(is_string($value))
						{
                            $paramType = \PDO::PARAM_STR;
						}
                        elseif(is_float($value))
						{
                            $paramType = \PDO::PARAM_STR;
						}
						elseif( is_null($value ))
						{
						    $paramType = \PDO::PARAM_NULL;
						}
                        else
						{
                            $paramType = null;
                            //$this->logger->warn( __LINE__ .' '. __FUNCTION__ .' '
                            //         .'有代码尝试绑定不支持类型的参数'.$key.'到SQL语句('.$_sql.')'); 
						}
						if(null==$paramType)
						{
                            $_stmt->bindParam($key,$value);
						}
						else
						{
                            $_stmt->bindParam($key,$value,$paramType);
						}
                    }
                }

            }
            else
            {
                $this->logger->trace( __LINE__ .' '. __FUNCTION__ .' '
                                     .'有代码尝试执行未含参数的SQL语句('.$_sql.')'); 
            }
            $_stmt->execute();  
        }
        catch (\PDOException  $e) 
        {  
            $this->logger->error( __LINE__ .' '. __FUNCTION__ .' '.'SQL语句('.$_sql.')执行失败'.$e->getMessage().",绑定参数为：".var_export($_param,true)); 
            return null;
        }  
        return $_stmt;  
    }        
    
    /**
     * PdoDB::get_results()
     * 
     * @param string $query
     * @param string $output,OBJECT 输出对象，ARRAY 输出数组
     * @return
     */
    public function get_results($query,$output = 'OBJECT',$param=null,$noindex=false)
    {
        if(null == $this->_pdo)
            return null;

        $rs = $this->execute($query,$param);
        $rtn = array();
		//增加一个执行语句错误时的判断，返回空的结果。调用方应判断列表长度
		if(null==$rs)
			return $rtn;
		
        if($output == 'OBJECT')
        {
            while($row = $rs->fetchObject())
            {
               array_push($rtn,$row);
            }
            return $rtn;
        }
        if($output == 'ARRAY')
        {
			if($noindex)
			{
                return $rs->fetchAll(\PDO::FETCH_ASSOC);
			}
			else
			{
				return $rs->fetchAll();
			}
        }

    }
    
 
    /**
     * PdoDB::get_var() 返回sql查询单值
     * 
     * @param mixed $query
     * @param integer $x
     * @param integer $y
     * @return
     */
    public function get_var($query,$x=0,$y=0,$param=null)
    {
        if(null == $this->_pdo)
        {
            return null;
        }
        $values = $this->get_results($query,'OBJECT',$param);
		if(count($values)==0)
		{
			return null;
		}
        $var = get_object_vars($values[$x]);
        $keys =  array_keys($var);
        $key = $keys[$y];
       
        return (isset($var[$key]) && $var[$key]!=='')?$var[$key]:null;
    }


    //获取数据表的字段信息，并存储在私有成员信息中
	function getColumnInfo($tablename)
	{
		//强制表名转小写，避免后续错误
		$tablename = strtolower($tablename);

		//去除二进制字段，这些字段无法自动插入
	    $sql=<<<SQL
			SELECT LOWER(column_name) as colname,LOWER(column_key) as keytype,LOWER(extra) as autoinc 
			  FROM information_schema.columns 
			  WHERE table_schema=:DBName AND table_name=:TableName AND data_type not in ('blob','longblob','blob','geometry')
SQL;
        $results=$this->get_results($sql,"ARRAY",array(":DBName"=>G_DB_NAME,":TableName"=>$tablename));

        //主键列和属性列数组
		$keycols=array();
		$propcols=array();
		$autoinckey='';

		$rownum=count($results);
		for($i=0;$i<$rownum;$i++)
		{
			$colname=$results[$i]["colname"];
		    //主键字段压入主键列数组，其他字段压入属性列数组
			if($results[$i]["keytype"]=="pri")
			{
				array_push($keycols,$colname);
				if($results[$i]["autoinc"]=='auto_increment')
				{
					$autoinckey=$colname;
				}
			}
			else
			{
				array_push($propcols,$colname);
			}
		}
		$this->tblcolinfo[$tablename]=array("keycols"=>$keycols,
			                   "propcols"=>$propcols,"autoinckey"=>$autoinckey);
	}


    /**
	 * 插入或更新对象到数据库指定的表，同时支持数组和对象
	**/ 
    public function insertOrUpdateObj2DB($recordobj,$tablename,$onlyinsert=false)
	{
		//判断传入的是不是对象和数据，且和类型是否相配
        if(is_object($recordobj))
		{
			$type="OBJECT";
		}
        elseif(is_array($recordobj))
		{
			$type="ARRAY";
		}
		else
		{
	        $this->logger->error( __LINE__ .' '. __FUNCTION__ .' 更新数据表('.$tablename.")时，传入的参数既不是对象，也不是数组。"); 	
			return FALSE;
		}

		$tablename = strtolower($tablename);
		//检查字段列表信息是否已经获得，如果尚未获得，则从系统表获取
		if(array_key_exists ($tablename ,  $this->tblcolinfo )==FALSE)
		{
		    $this->getColumnInfo($tablename );
		}

        $keycols = $this->tblcolinfo[$tablename]["keycols"];
        $propcols = $this->tblcolinfo[$tablename]["propcols"];
	    //$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' 数据表('.$tablename."获取的字段信息为："
		//                                .var_export($keycols,true).var_export($propcols,true)); 	

        //参数数数组，根据传入对象的属性对应列构造
		$parray = array();
		$keyarray = array();

        //插入列数组，根据传入对象的属性对应列构造
        $insertcol=array();

        //构造更新列
		$propcount = count($propcols);
		$propupdate = array();
        for($i=0;$i<$propcount;$i++)
		{
			if($type=="OBJECT") //对象方式
			{
				if(property_exists($recordobj,$propcols[$i]))
				{
					array_push($propupdate,$propcols[$i]." = :".$propcols[$i]);  //压入更新列数组
					array_push($insertcol,$propcols[$i]);                        //压入插入列数组
					$parray[":".$propcols[$i]] = $recordobj->{$propcols[$i]};    //参数数值赋值
				}
			}
			else //数组方式
			{
				if(array_key_exists($propcols[$i],$recordobj))
				{
					array_push($propupdate,$propcols[$i]." = :".$propcols[$i]);  //压入更新列数组
					array_push($insertcol,$propcols[$i]);                        //压入插入列数组
					$parray[":".$propcols[$i]] = $recordobj[$propcols[$i]];      //参数数值赋值
				}
			}
		}
		if(count($propupdate)==0)
		{
	        $this->logger->error( __LINE__ .' '. __FUNCTION__ .' 更新数据表('.$tablename.")时，未传入任何可更新的列。"); 	
			//return FALSE;
			$onlyinsert = true;
		}
		$propupdatestr = implode(",",$propupdate);

        //构造主键列
		$keycount = count($keycols);
		$keycondition = array();
        for($i=0;$i<$keycount;$i++)
		{

			if($type=="OBJECT") //对象方式
			{
				if(property_exists($recordobj,$keycols[$i]))
				{
					array_push($keycondition,$keycols[$i]." = :".$keycols[$i]);    //压入主键列数组
					array_push($insertcol,$keycols[$i]);                         //压入插入列数组
					$parray[":".$keycols[$i]] = $recordobj->{$keycols[$i]};    //参数数值赋值
					$keyarray[":".$keycols[$i]] = $recordobj->{$keycols[$i]};    //参数数值赋值
				}
				else
				{
					//对于自增字段插入时可以不提供，更新时需要提供，这个问题要程序员自己去保证或以后完善
					if($this->tblcolinfo[$tablename]["autoinckey"]==$keycols[$i])
					{
						continue;
					}
					$this->logger->error( __LINE__ .' '. __FUNCTION__ .' 前台程序传入的JSON对象未包含必须的主键字段('.$keycols[$i].')的信息。'); 	
					return "保存".$tablename."记录时未提供足够的记录唯一信息,如（".$keycols[$i].")字段的信息。";
				}
			}
			else
			{
				if(array_key_exists($keycols[$i],$recordobj))
				{
					array_push($keycondition,$keycols[$i]." = :".$keycols[$i]);    //压入主键列数组
					array_push($insertcol,$keycols[$i]);                         //压入插入列数组
					$parray[":".$keycols[$i]] = $recordobj[$keycols[$i]];    //参数数值赋值
					$keyarray[":".$keycols[$i]] = $recordobj[$keycols[$i]];    //参数数值赋值
				}
				else
				{
					//对于自增字段插入时可以不提供，更新时需要提供，这个问题要程序员自己去保证或以后完善
					if($this->tblcolinfo[$tablename]["autoinckey"]==$keycols[$i])
					{
						continue;
					}
					$this->logger->error( __LINE__ .' '. __FUNCTION__ .' 前台程序传入的JSON对象未包含必须的主键字段('.$keycols[$i].')的信息。'); 	
					return "保存".$tablename."记录时未提供足够的记录唯一信息,如（".$keycols[$i].")字段的信息。";
				}
			}
		}
		$keyconditionstr = implode(" AND ",$keycondition);

        //构造插入列字符串和插入参数字符串
		$insertcolname = implode(",",$insertcol);
		$insertcolvalue = ":".implode(",:",$insertcol);

		$usql= " UPDATE $tablename SET $propupdatestr  WHERE $keyconditionstr ";
		$isql= " INSERT INTO $tablename ( $insertcolname ) VALUES ( $insertcolvalue ) ";

        //如果只做插入，则执行如下语句，存在一个可能是插入失败，因此需要检查记录是否已经存在
        if($onlyinsert)
		{
			//如果主键是自增列，则插入时无须检查是否重复
			if(count($keyarray)>0)
			{
		        $fsql= " SELECT COUNT(1) AS recnum FROM $tablename WHERE $keyconditionstr ";
				$results=$this->get_results($fsql,"ARRAY",$keyarray);
			    $recnum = $results[0]["recnum"];//判断记录是否已经存在；
				//如果已经存在记录，则直接返回错误
                if($recnum>0)
				{
					return false;
				}
			}

			$stmt = $this->execute($isql,$parray);
		}
		else
		{
			$stmt = $this->execute($usql,$parray);
			$count = $stmt -> rowCount();//判断SQL语句影响的行数，若为O，说明更新不成功；
			//判断更新条数是否大于零
			if(0==$count)
			{
				$stmt = $this->execute($isql,$parray);
			}
		}
        return TRUE;
	}

    /**
	 * 返回指定表的对象列表，同时支持数组和对象，条件要求是数组
	**/ 
    public function getObjListFromDB($tablename,$condition=null,$output="ARRAY")
	{
		//判断传入的是不是对象和数据，且和类型是否相配
        if(is_object($condition))
		{
			$condition= get_object_vars($condition);
		}

        if(!is_array($condition) && $condition!=null)
		{
	        $this->logger->error( __LINE__ .' '. __FUNCTION__ .' 查询数据表('.$tablename.")时，传入的参数既不是对象，也不是数组。"); 	
			return FALSE;
		}


		$tablename = strtolower($tablename);
		//检查字段列表信息是否已经获得，如果尚未获得，则从系统表获取
		if(array_key_exists ($tablename ,  $this->tblcolinfo )==FALSE)
		{
		    $this->getColumnInfo($tablename );
		}
        $keycols = $this->tblcolinfo[$tablename]["keycols"];
        $propcols = $this->tblcolinfo[$tablename]["propcols"];

        //合并主键和属性列字段，并拼接查询语句
        $allcols  =  array_merge ( $keycols,$propcols);
		$querycolstr = implode(",",$allcols);

        //根据用户传入的查询字段拼接条件语句
		$condcols = array();
		$parray = array();
		foreach($condition  as  $k  =>  $v )
		{
	        $this->logger->debug( __LINE__ .' '. __FUNCTION__ .' 数据表('.$tablename.")查询条件为：".$k."=".$v); 	

			if(array_search($k,$allcols)!==false)
			{
				array_push($condcols,$k." = :".$k);  //压入条件列数组
				$parray[":".$k] = $v;                //参数数值赋值
			}
			else    //如果附加的参数键值不在表的字段里面，则按照附加条件追加在SQL后面
			{
				array_push($condcols,$v);  //压入条件列数组
			}
		}
		$conditionstr = implode(" AND ",$condcols);


		$sql= " SELECT $querycolstr FROM $tablename ";
		//如果提供了查询条件，追加SQL条件
		if(count($condcols)>0)
		{
			$sql=$sql." WHERE $conditionstr";
		}
	    $this->logger->debug( __LINE__ .' '. __FUNCTION__ .' 查询数据表('.$tablename.")时，自动生成的SQL语句为：".$sql); 	

		if($output=="OBJECT")
		{
		    return $this->get_results($sql,$output,$parray,true);
		}
		else
		{
		    return $this->get_results($sql,$output,$parray);
		}

	}

}

?>