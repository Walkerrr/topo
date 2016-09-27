<?php

//包含系统定义的自有配置信息
require_once '../tpss_conf.php';

//包含框架定义的配置信息
require_once 'config.inc.php';

//引入框架应用启动类
require_once '../class/system/Application.class.php';

//增加用户自定义错误处理程序
error_reporting(E_ALL);
$old_error_handler = set_error_handler("tpssErrorHandler");

$debug = false;

if($debug)
{
	//启动输出缓冲
	ob_start();
}

$app = \Application::getInstance();

$response = $app->run();

if($debug)
{
	//检查缓冲区内容
	$buffstr=ob_get_contents(); 
	//未来根据需要确定是否清除缓冲区
	//ob_end_clean();
	ob_end_flush(); 

	//写输出日志到文件
	$nowday = date ( "Y-m-d H:i:s" );
	$fh = fopen(G_ROOT_DIR."/log/outputlog".substr($nowday,0,10).".txt","a");

	fwrite ( $fh, "\r\n\r\n".$nowday."来自".$_SERVER['REMOTE_ADDR'] );
	fwrite ( $fh, "\r\n--------------\r\n");
	fwrite ( $fh, $_SERVER['REQUEST_URI']);//$_SERVER['QUERY_STRING']
	if($buffstr)
	{
		fwrite ( $fh, "\r\n--------------\r\n");
		fwrite ( $fh, $buffstr);
	}
	fwrite ( $fh, "\r\n--------------\r\n");
	$resp=json_decode($response);
	if(!$resp)
	{
		$resp=$response;
	}

	fwrite ( $fh, var_export($resp,true));
	fclose($fh);
}

echo $response;


function tpssErrorHandler($errno,$errstr,$errfile,$errline)
{
    if (!(error_reporting() & $errno)) 
	{
         // This error code is not included in error_reporting
         return;
    }

	$nowday = date ( "Y-m-d H:i:s" );
	$fh = fopen(G_ROOT_DIR."/log/errorlog".substr($nowday,0,10).".txt","a");

	fwrite ( $fh, "\r\n\r\n".$nowday."来自".$_SERVER['REMOTE_ADDR'] );
	fwrite ( $fh, "\r\n--------------\r\n");
	fwrite ( $fh, $_SERVER['REQUEST_URI']);

	fwrite ( $fh, "\r\n文件:$errfile");
	fwrite ( $fh, "\r\n行号:$errline");
	fwrite ( $fh, "\r\n错误代码:$errno");
	fwrite ( $fh, "\r\n错误信息:$errstr\r\n");
	fclose($fh);

    /* Don't execute PHP internal error handler */
    return  true ;
}




?>