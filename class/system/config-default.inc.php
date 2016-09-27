<?php
/**
 * @link http://www.dimpt.com/
 * @copyright Copyright (c) 2015 CITC
 * @license 
 *
 * 缺省配置文件，如果应用目录中重复定义相应的常量，则本文件中定义的常量无效
 *
 * @author lifq
 * @since 1.0
 */
 
/**
 * 类根目录定义
 */
defined('CLASS_PATH') or define('CLASS_PATH','class');

/**
 * 应用目录定义，该常量必须在应用目录中重新定义
 */
defined('APP_NAME') or define('APP_NAME','test');

/**
 * 缺省的UI类定义，当用户输入的参数c无效时，转向此类
 */
defined('DEFAULT_UICLASS') or define('DEFAULT_UICLASS','\\framework\\defaultUI');

/**
 * 缺省的Action定义，当用户输入的参数a无效时，转向此类
 */
defined('DEFAULT_UIACTION') or define('DEFAULT_UIACTION','default');

/**
 * 缺省的AccessControl，应用模块如果需要访问控制，需重写该类
 */
defined('ACL_CLASS') or define('ACL_CLASS','\\framework\\DefaultACL');

/**
 * 缺省的LOGIN_TIMEOUT，用户登录超时退出间隔，单位分钟
 */
defined('LOGIN_TIMEOUT') or define('LOGIN_TIMEOUT',300);


    
?>