<?php
if (!defined('THINK_PATH')) exit();

$dbcnf = require_once( 'db.php' );
$cnf =  array(
	'APP_STATUS'            => 'debug',
    'APP_SUB_DOMAIN_DEPLOY' => false,
	'COOKIE_PREFIX' => 'izhimo_',
	'SESSION_AUTO_START'    => false,
	'SESSION_TYPE' => 'File',
	'TOKEN_ON'           => false,
	'TMPL_CONTENT_TYPE'     => 'text/html',
    'TMPL_TEMPLATE_SUFFIX'  => '.html',
    'TMPL_FILE_DEPR'=>'/',
	'LOG_RECORD' => false,
    'LOG_LEVEL' =>'EMERG,ERR',
    'HTML_CACHE_ON' => false, //禁止静态缓存
    'TMPL_CACHE_ON' =>  false, //关闭模版缓存
	'URL_CASE_INSENSITIVE'  => false,
    'URL_MODEL'             => 1,//2,
    'URL_HTML_SUFFIX'       => '.html',
	'URL_ROUTER_ON'      => true,
	'URL_CASE_INSENSITIVE' => TRUE,
	'MODE_ALLOW_LIST' =>'Home,Admin',
	'DB_FIELDTYPE_CHECK'=>true, //是否进行字段类型检查
	'DATA_CACHE_SUBDIR'=>true,//哈希子目录动态缓存的方式
    'TMPL_ACTION_ERROR'     =>TMPL_PATH.'jumpurl.html', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   => TMPL_PATH.'jumpurl.html', // 默认成功跳转对应的模板文件
    'APP_GROUP_LIST'     => 'Home,Admin',
    'DEFAULT_GROUP'      => 'Home',
	'SITE_NAME'          => 'izhimo 免费小说站',
	'SITE_URL'           => '/',
    'DOMAIN_NAME'        => 'i织墨小说站',
	'U_HASH_KEY'        => 'U&8f',
	'U_AUTH_KEY'        => '_nvau',
	'U_UPLOAD_DIR'     =>'skdata/',
	'U_UPLOAD_DIRPATH' =>'Y/m/',
	'IMG_WATER'        => 1, //图片水印
	'IMG_WATER_PIC'   => 'public/water.png'
);

return array_merge( $cnf , $dbcnf );
?>