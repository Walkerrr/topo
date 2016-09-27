<?php
/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.dimpt.com/
 */
namespace utils;

/**
 * 对HTTP参数获取的统一封装
 * 默认采用$_ REQUEST获取参数值，后期如果有优化需求，可以改为用$_GET或 $_POST获取值
 * @author songqj
 * @since 1.0
 *
 * change:2015-4-24 1.创建初始版本
 * change:2015-5-09 1.增加浮点参数获取支持
 */

class HttpParam
{
    private $logger;                                     //日志对象
    private static $_instance = null;            //用于存放实例化的HttpParam对象 

    private function __construct() 
    {  
        $this->logger = \Logger::getLogger( __CLASS__ );
    }  

    public static function getInstance()       
    {       
        if (null == self::$_instance)       
        {       
            self::$_instance = new self();       
        }       
        return self::$_instance;       
    }       


		/**
	 * 转换字符串为布尔值，可以接受的真值字符串为"yes","on","true","1"
	 *                                                  假值字符串为"no","off","false","0" 
	 *                                   除以上字符串外，统一返回假值，并记录日志 
	 * @param string $strParam
	 * @return bool
	 */
	function convStr2Boolean( $strParam ) {
		if( 0 == strcasecmp( 'off', $strParam ) || 0 == strcasecmp( 'no', $strParam ) 
			|| 0 == strcasecmp( 'false', $strParam ) || 0 == strcasecmp( '0', $strParam ) ) 
		{
			return false;
		}
		else if( 0 == strcasecmp( 'on', $strParam ) || 0 == strcasecmp( 'yes', $strParam ) 
			|| 0 == strcasecmp( 'true', $strParam ) || 0 == strcasecmp( '1', $strParam ) ) 
		{
			return true;
		}
		else 
		{
	        $this->logger->warn( __LINE__ .' '. __FUNCTION__ .' 发现未知的输入值:('.$strParam.' )'); 
			return false;
		}
	}
	
	/**
	 * 获取http提交参数的字符串值.
	 * 后台程序调用该函数时，也可以提供默认值，如果前台未提供该参数，后台程序也没有提供默认值，则返回null
	 * 本函数不提供诸如?k=v1&k=v2&k=v3数组形式的参数传递
	 * @param string $paramName
	 * @param string $defaultStr (可选参数)
	 * @return null 或 string
	 */
	function getString( $paramName, $defaultStr = null ) 
	{
		if( isset( $_REQUEST[$paramName] ) ) 
		{
			$result =  $_REQUEST[$paramName];
		}
		else if( func_num_args() > 1 ) 
		{
			$result = $defaultStr;
		} 
		else
		{
	        //$this->logger->warn( __LINE__ .' '. __FUNCTION__ .' 调用方未传入（'.$paramName.'） 参数，后台程序也未提供默认值。'); 
			$result = null;
		}
		return $result;
	}

	/**
		 * 获取http提交参数的整数值.
		 * 后台程序调用该函数时，也可以提供默认值，如果前台未提供该参数，后台程序也没有提供默认值，则返回null
		 * @param string $paramName
		 * @param int $defaultValue (可选参数)
		 * @return null 或 int
	 */
	function getInt( $paramName, $defaultValue = null ) 
	{

		//$args = func_get_args();
		//$result = call_user_func_array( 'getString', $args );

        $retStr = $this->getString( $paramName);
		if($retStr!=null)
		{
			$retStr = str_replace(' ', '', trim( $retStr));
			if( !preg_match( "/^-?([0-9])*$/", $retStr ) ) 
			{
				$this->logger->warn( __LINE__ .' '. __FUNCTION__ .' 前台程序传入的（'.$paramName.'）参数值（'.$retStr.'）不是合法的整数，本函数返回的值为0。'); 	
			}
			return intval($retStr);
		}
		return $defaultValue;
	}


	/**
		 * 获取http提交参数的浮点数值.
		 * 后台程序调用该函数时，也可以提供默认值，如果前台未提供该参数，后台程序也没有提供默认值，则返回null
		 * @param string $paramName
		 * @param float $defaultValue (可选参数)
		 * @return null 或 float
	 */
	function getFloat( $paramName, $defaultValue = null ) 
	{
		//$args = func_get_args();
		//$result = call_user_func_array( 'getString', $args );

        $retStr = $this->getString( $paramName);
		if($retStr!=null)
		{
			return floatval($retStr);
		}
		return $defaultValue;
	}

	/**
		 * 获取http提交参数的JSON对象.
		 * 后台程序调用该函数时，不支持提供默认值，如果前台未提供该参数，则返回null
		 * @param string $paramName
		 * @return null 或 JSON Object
	 */
	function getJsonObj( $paramName) {
		$result = $this->getString( $paramName );

		if (null==$result or ''==trim($result))
		{
	        $this->logger->warn( __LINE__ .' '. __FUNCTION__ .' 前台程序传入的JSON对象（'.$paramName.'）参数值（'.$result.'）为空字符串，本函数返回的值为空。'); 	
			return null;
		}
		//此处后期要增加JSON格式校验功能
		if(true == $this->validJSON($result ))
		{
			return json_decode($result);
		}
		else
		{
	        $this->logger->warn( __LINE__ .' '. __FUNCTION__ .' 前台程序传入的JSON对象（'.$paramName.'）参数值（'.$result.'）不是合法的JSON格式，本函数返回的值为空。'); 	
			return null;
		}

	}
	
	
	
	/**
	 * 获取http提交参数的布尔值.
	 * @param string $paramName
	 * @param bool $defaultValue (可选参数)
	 * @return bool|null
	 */
	function getBool( $paramName, $defaultValue = false ) {
		$result = $this->getString( $paramName, $defaultValue );
		return $this->convStr2Boolean( $result );
	}


	/**
	 * 检查前台是否提交了指定参数
	 * @param string $paramName
	 * @return bool
	 */
	function existParam( $paramName ) 
	{
		if( isset( $_REQUEST[$paramName] ) ) 
		{
			return true;
		}		
		return false;
	}


	/**
	 * 检查前台提交的JSON对象参数格式是否正确
	 * @param string $jsonStr
	 * @return bool
	 */
	function validJSON( $jsonStr ) 
	{
		//目前对所有代码均返回有效，并记录日志
	    //$this->logger->warn( __LINE__ .' '. __FUNCTION__ .' 前台程序传入的JSON对象参数值为('.$jsonStr.')' ); 	
		return true;
	}	

	/**
	 * 获取一个cookie变量
	 * @param string $p_var_name
	 * @param string $p_default
	 * @return string
	 */
	function getCookie( $paramName, $defaultValue = null )
	{
		if( isset( $_COOKIE[$paramName] ) ) {
			$result = htmlspecialchars($_COOKIE[$p_var_name] );		
		}
		else if( func_num_args() > 1 ) 
		{
			$result = $defaultValue;
		} 
		else 
		{
	        $this->logger->warn( __LINE__ .' '. __FUNCTION__ .' 尚未存在要访问的cookit变量('.$paramName.')，且未提供默认值' ); 	
			$result = $defaultValue;
		}
		return $result;
	}

	/**
	 * 设置一个cookie变量
	 * @param string $paramName
	 * @param string $paramValue
	 * @param bool $paramExpire 默认值 false
	 * @return bool - true 成功, false 失败
	 */
	function setCookie( $paramName, $paramValue, $paramExpire = false ) 
	{
		if( false === $paramExpire ) {
			$paramExpire = 0;
		}
		else if( true === $paramExpire ) {
			$t_cookie_length = 60*60*24;
			$paramExpire = time() + $t_cookie_length;
		}

		return setcookie( $paramName, $paramValue, $paramExpire );
	}

	/**
	 * 清除一个cookie值
	 * @param string $p_name
	 * @return bool
	 */
	function clearCookie( $paramName ) 
	{
		if( isset( $_COOKIE[$paramName] ) ) {
			unset( $_COOKIE[$paramName] );
		}
		if( !headers_sent() ) {
			return setcookie( $paramName, '', -1 );
		} 
		else 
		{
			return false;
		}
	}	
	
	
}
