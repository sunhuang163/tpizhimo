<?php
if (!defined('THINK_PATH')) exit();

$dbcnf = require_once( 'db.php' );
$cnf =  array(
	'APP_STATUS'            => 'debug',  
    'APP_SUB_DOMAIN_DEPLOY' => false,   
	'COOKIE_PREFIX' => 'izhimo_',
	'SESSION_AUTO_START'    => false,
	'TOKEN_ON'           => false,
	'TMPL_CONTENT_TYPE'     => 'text/html', 
    'TMPL_TEMPLATE_SUFFIX'  => '.html',   
    'TMPL_FILE_DEPR'=>'/',
	'URL_CASE_INSENSITIVE'  => false,     
    'URL_MODEL'             => 2,         
    'URL_HTML_SUFFIX'       => '.html',  
	'URL_ROUTER_ON'      => true,              
    'APP_GROUP_LIST'     => 'Home,Admin',	 
    'DEFAULT_GROUP'      => 'Home',	   
	'SITE_NAME'          => 'izhimo 免费小说站',	 
    'DOMAIN_NAME'        => 'http://www.izhimo.com/',
	'U_HASH_KEY'        => 'UHFfd7329@~883',
);

return array_merge( $cnf , $dbcnf );
?>