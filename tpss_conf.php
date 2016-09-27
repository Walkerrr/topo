<?php

//$gRootDir = $_SERVER['DOCUMENT_ROOT'];
$gRootDir = "D:/wamp/www";
##定义网站根目录
define("G_ROOT_DIR","D:/wamp/www");

##自动包含日志类和配置文件
require_once $gRootDir."/class/system/utils/log4php_2.3.0/Logger.php";
Logger::configure($gRootDir."/log_conf.xml");
require_once $gRootDir."/class/system/utils/PHPExcel_1.8.0/Classes/PHPExcel.php";//包含PHPEXCEL类


##定义Excel上传和导出文件目录
define("G_EXCEL_DIR","");


##设置当前时区
ini_set('date.timezone','Asia/Shanghai');

## 数据库全局设置
define("G_DB_TYPE","mysql");
define("G_DB_SERVER","localhost");
define("G_DB_NAME","sdn");
define("G_DB_CHARSET","utf8");
define("G_DB_PORT","3306");
define("G_DB_USER","root");
define("G_DB_PASSWORD","root");

##自动包含数据库连接类
require_once $gRootDir."/class/system/db/PdoDB.class.php";

##规划计算基准年
define("G_CALC_YEAR","2014");

##定义自由查询返回的最大行数
define("G_MAX_QUERYROWS",500);

?>