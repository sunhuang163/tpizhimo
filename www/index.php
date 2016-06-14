<?php
/**
* izhimo项目的入口文件
*
* 将程序框架升级到tp5.0 RC3支持php7
*
* @author 	banfg56
* @Project	izhimo
* @date 	2014/5/21
*/

define('APP_PATH', __DIR__.'/../app/');
define('__ROOT__', __DIR__ . '/' );
define('RUNTIME_PATH', APP_PATH.'runtime/' );
define('EXTEND_PATH', APP_PATH.'/lib/extend/' );
define('VENDOR_PATH', APP_PATH.'vendor/' );

define('APP_DEBUG',  true);
if( !APP_DEBUG  )
{
	//屏蔽PHP错误信息
	@ini_set('display_errors',false);
	@error_reporting(0);
}
// 加载框架入口文件
require APP_PATH. 'lib/thinkphp/start.php';
?>