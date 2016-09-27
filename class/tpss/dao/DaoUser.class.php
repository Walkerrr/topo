<?php
/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.dimpt.com/ 
 */
namespace dao;

/**
 * t_User表操作数据库封装类
 *
 * @property 
 *
 * @author lifq
 * @since 1.0
 * @change:2015-5-10 1.修改类名为DaoUser, songqj
 *                   2.增加库内密码存储为加盐hash，使用password_hash和password_verify
 */
class DaoUser{
    private $logger;                   //日志对象
    private $pdodb;
    private $sess;
    
    public function __construct(){
        $this->logger = \Logger::getLogger( __CLASS__ );
		$this->pdodb = \db\PdoDB::getInstance();
        $this->sess = \framework\Session::getInstance();
    }    

    /**
     * 用户登录函数.修改口令的时候会调用该函数
     * @param string $userName 用户名
     * @param string $psw 用户密码
     * @return boolean 登录成功返回TRUE，否则返回FALSE
     * @throws 
     */     
	public function login($userName,$psw,$chgPwd=false)
	{

        /**暂时先不做检查
		//每次有用户登录时就清空其他超时的用户登陆
		$dt = time()-LOGIN_TIMEOUT*60;
		$this->pdodb->execute("UPDATE t_user SET sessionid=null where lastupdate<=?;",array($dt));
		**/

        //去除口令前后的空格，避免产生歧义
		$psw=trim($psw);

		//如果sessionid为null，则说明该用户尚未登录
		$sql = "SELECT fullname,level,defaultregion,sessionid,pwdhash,temppwdhash,isadmin FROM t_user WHERE username = :UserName and enabled='1';";
		$rs = $this->pdodb->get_results($sql,"ARRAY",array(":UserName"=>$userName));

        if(1==count($rs))
		{
			$sql = "UPDATE t_user SET lastupdate=:LastUpdate,sessionid=:SessionID where username=:UserName;";
			$parray = array(
						":LastUpdate" =>date("Y-m-d H:i:s"),
						":SessionID"  =>$this->sess->id,
						":UserName"   =>$userName);

            $verify = true; 
			/*
			if(password_verify($psw,$rs[0]["pwdhash"]))
			{
	            $verify = true; 
			}
			elseif(password_verify($psw,$rs[0]["temppwdhash"]))
			{
				if(!$chgPwd)
				{
					$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'用户('
		                           .$userName.')使用临时口令登录系统.' ); 
				}
	            $verify = true; 
			}
			*/
			if($verify)
			{
				//更新用户最后登录时间和当前会话ID
				$this->sess->login_userName = $userName;
				$this->sess->login_fullName = $rs[0]["fullname"];
				$this->sess->login_userLevel = $rs[0]["level"];
				$this->sess->isadmin = $rs[0]["isadmin"];
				$this->sess->calcyear = G_CALC_YEAR;
				$defregion = $rs[0]["defaultregion"];

				$this->sess->login_region = $defregion;
                //暂时关闭用户会话ID入库功能
				$this->pdodb->execute($sql,$parray);
				$this->sess->cityid =$defregion;
				/*
				$sql = "select provinceid,provincename,cityname from t_city where cityid=:CityID";
				$rs = $this->pdodb->get_results($sql,"ARRAY",array(":CityID"=>$defregion));

				if(1==count($rs))
				{
					$this->sess->cityname =$rs[0]["cityname"];
					$this->sess->provinceid =$rs[0]["provinceid"];
					$this->sess->provincename =$rs[0]["provincename"];

				}
				else
				{
					$this->logger->error( __LINE__ .' '. __FUNCTION__ .' '.'用户('
		                           .$userName.')的默认区域('.$defregion.')不是有效的本地网编码.' ); 

				}
				*/

			    return true;

			}

		}
		return false;
	}

		
    /**
     * 更新用户状态，并返回登录状态.
     * @param 
     * @return Result 已登录返回TRUE，否则返回FALSE
     * @throws 
     */     
    public function status(){
	
        //如果未登录，则直接返回
        if(!isset($this->sess->login_userName))
            return \Framework\Result::Error('用户未登录！');

        /* 暂时屏蔽单人登录和超时限制   
        //从数据库中查找用户当前回话的上次更新时间
        $str_q = "SELECT lastupdate FROM t_user WHERE username = ? and sessionid=?;";
        $param = array($this->sess->login_userName,$this->sess->id);
        $rs = $this->pdodb->execute($str_q,$param);
		if(!$row = $rs->fetchObject())
		{
		    //该用户已在其他地方登陆  
            $this->sess->destroy();
            return \Framework\Result::Error('该用户已在其他地方登陆！');
        }

        $nowTime = new \DateTime(date("Y-m-d H:i:s"));
        $nowTime->sub(new \DateInterval('PT'.LOGIN_TIMEOUT.'M'));
        $nowStamp = $nowTime->format("Y-m-d H:i:s");		
		
        //如果登陆时间大于设定值，则超时退出登录

        if($nowStamp > $row->lastupdate)
        {
        	$str_q_u = "UPDATE t_user SET sessionid=null where username = ? and sessionid=?;";
        	$this->pdodb->execute($str_q_u,$param);
            $this->sess->destroy();
            return \Framework\Result::Error('用户登陆超时！');
        }
        */

        $str_q_u = "UPDATE t_user SET lastupdate=? where username = ? and sessionid=?;";
        $param = array(date("Y-m-d H:i:s"),$this->sess->login_userName,$this->sess->id);
        $this->pdodb->execute($str_q_u,$param);
		$userinfo=array();
		$userinfo["username"]=$this->sess->login_userName;
		$userinfo["fullname"]=$this->sess->login_fullName;
		$userinfo["userlevel"]=$this->sess->login_userLevel;		
		$userinfo["isadmin"]=$this->sess->isadmin;
		$userinfo["cityid"]=$this->sess->cityid;
		$userinfo["cityname"]=$this->sess->cityname;
		$userinfo["provinceid"]=$this->sess->provinceid;
		$userinfo["provincename"]=$this->sess->provincename;
		$userinfo["calcyear"]=$this->sess->calcyear;
        return \Framework\Result::OK('用户已登录！',$userinfo);        
    }
    
    /**
     * 获取用户的全部信息.
     * @param 
     * @return Result 已登录返回TRUE，否则返回FALSE
     * @throws 
     */     
    public function getUserInfo()
    {
        $sql = <<<SQL
        SELECT username,fullname,email,mobilephone,level,defaultregion,isadmin,
			   sessionid,lastupdate,enabled,department
			FROM t_user WHERE username=?
SQL;

        $rs = $this->pdodb->get_results($sql,"OBJECT",array($this->sess->login_userName));
        return $rs ;
    }  

    /**
     * 编辑用户本人的信息.
     * @param 
     * @return Result 已登录返回TRUE，否则返回FALSE
     * @throws 
     */     
    public function editUserInfo($fullName,$email,$mobilePhone,$department,$defaultRegion)
    {
            
        $sql = "UPDATE t_user SET fullname=?,email=?,mobilephone=?,department=?,defaultregion=? WHERE username=?";
        $param = array($fullName,$email,$mobilePhone,$department,$defaultRegion,$this->sess->login_userName);
        $this->pdodb->execute($sql,$param);
		$this->logger->warn( __LINE__ .' '. __FUNCTION__ .' '.'用户('.$this->sess->login_userName.
			    ")修改了基本信息：$fullName,$email,$mobilePhone,$department,$defaultRegion"); 

        return true;
    }
    
    /**
     * 返回系统中的用户列表，必须拥有超级用户权限，否则不允许.
     * @param 
     * @return Array 用户列表
     * @throws 
     */   
    public function getAllUserList($queryname)
    {
		//如果未提供查询条件，则返回所有用户
		if(null==$queryname)
		{
			$sql = "SELECT username,fullname,email,mobilephone,level,defaultregion,isadmin,department,enabled,provincename,cityname "
		         ." FROM t_user left join t_city on defaultregion=cityid order by level,isadmin desc,defaultregion";
  			$result = $this->pdodb->get_results($sql);
		}
		else
		{
			$sql = "SELECT username,fullname,email,mobilephone,level,defaultregion,isadmin,department,enabled,provincename,cityname "
		         ." FROM t_user INNER JOIN t_city on defaultregion=cityid  "
				 ." WHERE username like :queryname OR fullname LIKE  :queryname OR (t_city.provincename LIKE :queryname) "
				 ." ORDER BY level,isadmin desc,defaultregion";

  			$result = $this->pdodb->get_results($sql,"ARRAY",array(":queryname"=>"%".$queryname."%"));

		}
        return $result;
    }  
    
    /**
     * 增加用户，必须拥有超级用户权限，否则不允许.
     * @param string $userName 增加的用户名
     * @return 
     * @throws 
     */       
    public function addUser($userName,$fullName,$email,$level,$defaultRegion,$mobilePhone,$isAdmin,$department,$enabled)
    {
        $sql = <<<SQL
        INSERT INTO t_user(username,fullname,email,mobilephone,level,defaultregion,isadmin,department,enabled) 
			VALUES (:UserName,:FullName,:Email,:MobilePhone,:Level,:DefaultRegion,:IsAdmin,:Department,:Enabled);
SQL;

		$parray=array(
			":FullName"=>$fullName,
			":Email"=>$email,
			":MobilePhone"=>$mobilePhone,
			":Level"=>$level,
			":IsAdmin"=>$isAdmin,
			":DefaultRegion"=>$defaultRegion,
			":Department"=>$department,
			":Enabled"=>$enabled,
			":UserName"=>$userName
			);
        $this->pdodb->execute($sql,$parray);
    }  

    /**
     * 停用、启用及删除用户，必须拥有超级用户权限，否则不允许.
     * @param string $userName 要操作的用户名
     * @param string $enabled 操作类型："0"-停用 "1"-启用 "9"-删除
     * @return 
     * @throws 
     */     
    public function enabledUser($userName,$enabled)
    {

        if($userName=='sys')
		{
			//禁止修改系统超级用户信息
			return false;
		}

        $opName=array("0"=>"停用","1"=>"启用","0"=>"删除");
        $sql = "UPDATE t_user SET enabled=:Enabled WHERE username=:UserName;";
		$parray=array(
			":Enabled"=>$enabled,
			":UserName"=>$userName
			);
        $this->pdodb->execute($sql,$parray);
		if(array_key_exists($enabled,$opName))
		{
            $this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'系统管理员('
		               .$this->sess->login_userName.')'.$opName[$enabled].'用户('.$userName.').'); 
		}
		else
		{
            $this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'系统管理员('
		               .$this->sess->login_userName.')对用户('.$userName.')进行了未知操作.'); 
		}

    }
    
    /**
     * 编辑其他用户信息，必须拥有超级用户权限，否则不允许.
     * @param 
     * @return Result 已登录返回TRUE，否则返回FALSE
     * @throws 
     */     
    public function updateUserInfo($userName,$fullName,$email,$level,$defaultRegion,$mobilePhone,$isAdmin,$department,$enabled)
	{
        if($userName=='sys')
		{
			//禁止修改系统超级用户信息
			return false;
		}
        $sql = <<<SQL
			UPDATE t_user SET fullName=:FullName,
			                  email=:Email,
			                  mobilephone=:MobilePhone,
			                  level=:Level,
			                  isadmin=:IsAdmin,
			                  department=:Department,
			                  enabled=:Enabled,
			                  defaultregion=:DefaultRegion
		        WHERE username=:UserName;
SQL;
		$parray = array(
			":FullName"=>$fullName,
			":Email"=>$email,
			":MobilePhone"=>$mobilePhone,
			":Level"=>$level,
			":IsAdmin"=>$isAdmin,
			":Department"=>$department,
			":Enabled"=>$enabled,
			":DefaultRegion"=>$defaultRegion,
			":UserName"=>$userName
			);
                    
        $this->pdodb->execute($sql,$parray);
    }    

    /**
     * 重置用户口令，必须拥有超级用户权限，如果用户自己重置，需持有临时许可.
     * 在本系统中，由于重置口令不改变现有的口令，因此无需临时凭证.
     * 重置口令需设置时间间隔，防止用户被恶意骚扰,目前设置为不小于12小时
	 * 理论上超级用户亦不应知道用户的密码，因此新的密码应为随机字符，自动发送到用户邮箱
	 * 重置口令时，原则上不改常规口令，需用临时密码登录后修改常规密码
     */     
    public function resetUserPwd($userName,$tempPwd='tpss',$email=null)
	{
        if(strtolower($userName)=='sys')
		{
			//禁止修改系统超级用户信息
			return false;
		}

		if(!filter_var($email,FILTER_VALIDATE_EMAIL))
		{
			$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'用户('.$userName.')的电子邮箱('.$email.')格式错误。'); 
			return false;
		}

        $sql = <<<SQL
			UPDATE t_user SET temppwdhash=:TempPwdHash,lastresettime=:LastResetTime
		        WHERE username=:UserName AND (:Email is null OR email=:Email)
			          #AND (lastresettime IS NULL OR lastresettime< DATE_SUB(:LastResetTime,INTERVAL 12 HOUR));
SQL;
        $nowTime = date("Y-m-d H:i:s");
		$parray = array(
			":TempPwdHash"=>password_hash($tempPwd,PASSWORD_DEFAULT),
			":UserName"=>$userName,
			":Email"=>$email,
			":LastResetTime"=>$nowTime
			);
		
        $this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.$userName.$tempPwd.$email.')'); 
                    
        $stmt=$this->pdodb->execute($sql,$parray);
		$count = $stmt -> rowCount();
		if($count>0)
		{
			if($userName==$this->sess->login_userName)
			{
				$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'系统管理员('
						   .$this->sess->login_userName.')重置了用户('.$userName.')的口令。'); 
			}
			else
			{
				$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'用户('
						   .$userName.')因遗忘密码申请设置新密码。'); 
			}
			return true;
		}
		else
		{
			$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'有人尝试重置用户('.$userName
				                .')的口令，由于用户名错误或者邮箱地址不匹配或两次重置时间小于12小时未成功。'); 
			return false;
		}

    }    

    /**
     * 修改用户口令，用户可以用临时口令或老的口令作为凭证,不需要传入用户名
     */     
    public function modifyUserPwd($newPwd,$oldPwd,$userName='')
	{
        //去除新旧口令前后的空格
        $newPwd=trim($newPwd);
        $oldPwd=trim($oldPwd);

		if($userName=='')
		{
			$userName=$this->sess->login_userName; 
		}

        $sql = <<<SQL
			UPDATE t_user SET temppwdhash='',pwdhash=:PwdHash
		        WHERE username=:UserName;
SQL;
		$parray = array(
			":PwdHash"=>password_hash($newPwd,PASSWORD_DEFAULT),
			":UserName"=>$userName
			);
        if($this->sess->login_userName == $userName)
		{
			if($this->login($userName,$oldPwd,true))
			{
				$this->pdodb->execute($sql,$parray);
				$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'用户('
							   .$this->sess->login_userName.')修改了自己的口令.');
				return "新密码已经设定成功，下次登录系统请使用新密码。";

			}
			else
			{
				return "当前密码不正确，请输入正确的当前密码。如果你已忘记当前密码，可以尝试通过找回密码来设定新密码。";
			}
		}
		else
		{
			$this->logger->debug( __LINE__ .' '. __FUNCTION__ .' '.'用户('
						   .$this->sess->login_userName.')尝试修改用户('.$userName.')的口令.'); 
			return "你无权设定他人的密码，系统已经对你的违规操作记录在案。";
		}

    }    	  

}