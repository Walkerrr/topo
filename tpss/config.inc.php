<?php
/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license http://www.dimpt.com/
 *
 * 应用配置文件，重复定义相应的常量，覆盖缺省值
 *
 * @author lifq
 * @since 1.0
 */
 

/**
 * 应用目录定义，该常量必须在应用目录中重新定义
 */
define('APP_NAME','tpss');

/**
 * CNSS重新定义的AccessControl
 */
define('ACL_CLASS','\\user\\ACL');
define('LOGIN_CLASS','ui\\Login');
    
?>