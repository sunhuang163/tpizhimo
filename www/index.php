<?php
/**
* izhimo项目的入口文件
*
* @author 	banfg56
* @Project	izhimo
* @date 	2014/5/21
*/
define('APP_NAME', '');
define('APP_PATH', './../app/');
define('APP_DEBUG',TRUE);
if( !APP_DEBUG )
{
	//屏蔽PHP错误信息
	@ini_set('display_errors',false);
	@error_reporting(0);
}
// 加载框架入口文件
require( APP_PATH."Lib/ThinkPHP/ThinkPHP.php");
?>