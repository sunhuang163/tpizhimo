<?php
if (!defined('THINK_PATH')) exit();

$dbcnf = require_once('db.php' );
$catecnf = require_once('cate.php');
$cnf =  array(
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
    'HTML_CACHE_ON' => false, /*禁止静态缓存 */
	'DATA_CACHE_TYPE' => "File", /*缓存配置 */
	'DATA_CACHE_COMPRESS' => true,
	'DATA_CACHE_SUBDIR'=>true,/*哈希子目录动态缓存的方式 */
	'DATA_PATH_LEVEL'=>3,
	'MODE_ALLOW_LIST' =>'Home,Admin',
	'DB_FIELDTYPE_CHECK'=>true, /*是否进行字段类型检查 */
    'TMPL_ACTION_ERROR'     =>TMPL_PATH.'jumpurl.html', /* 默认错误跳转对应的模板文件 */
    'TMPL_ACTION_SUCCESS'   => TMPL_PATH.'jumpurl.html', /* 默认成功跳转对应的模板文件 */
    'APP_GROUP_LIST'     => 'Home,Admin',
    'DEFAULT_GROUP'      => 'Home',
	'SITE_NAME'          => 'izhimo 免费小说站',
	'SITE_URL'           => 'http://www.nv.com/',
	'PIC_URL'            =>'http://www.nv.com/',
    'DOMAIN_NAME'        => 'i织墨小说站',
	'U_HASH_KEY'        => 'U&8f',
	'U_AUTH_KEY'        => '_nvau',
	'U_UPLOAD_DIR'     =>'skdata/',
	'U_UPLOAD_DIRPATH' =>'Y/m/',
	'U_HTML_DIR'       => 'html/', /* HTML 静态页面生成目录 */
	'U_CONTENT_REPLACE' => '', /*内容页面默默认配置替换选项*/
    'data_cache_novel' => 'mysqlnovel',
    'ST_PATH' => '/static/',
    'ST_PATH_PUBLIC' => '/public/',
	'IMG_WATER'        => 1, //图片水印
	'IMG_WATER_PIC'   => 'public/water.png',
	'IMG_SIZES'   => array(
	          array('w'=>'120','h'=>'150')
			 ),
	'URL_MODEL'             => 2, //项目ULR配置
	'URL_CASE_INSENSITIVE' => TRUE,
    'URL_HTML_SUFFIX'       => '',
	'URL_ROUTER_ON'      => true,
	'URL_ROUTE_RULES'  => array(
		'zimu/:zimu' =>'Cate/zimu?p=1',
		'zimu/:zimu_:p' =>  'Cate/zimu',
		'/^(\w+)$/'  => 'Cate/index?=url:1',
		':catename/:url' => 'Novel/index',
	),
);

return array_merge( $cnf , $dbcnf ,$catecnf );
?>