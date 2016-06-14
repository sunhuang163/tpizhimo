<?php
return [
		'url_domain_deploy' => false,
		'session' => [
		    'id'             => '',
		    'var_session_id' => '',  // SESSION_ID的提交变量,解决flash上传跨域
		    'prefix'         => 'izhimo_', // session 前缀
		    'type'           => 'File',  // 驱动方式 支持redis memcache memcached
		    'auto_start'     => false,  // 是否自动开启 session
		],
	    'log'  => [
		    'type' => 'File',  // 日志记录方式，支持 file socket trace sae
		    'level' => 'EMERG,ERR',
		    'path' => LOG_PATH,  // 日志保存目录
		],

	    'template'               => [
		    'type'         => 'Think',
		    'cache_on'	   => false,
		    'view_path'    => '',
		    'view_suffix'  => '.html',
		    'view_depr'    => DS,
		    'tpl_begin'    => '{',
		    'tpl_end'      => '}',
		    'taglib_begin' => '<',
		    'taglib_end'   => '>',
		],
		// 视图输出字符串内容替换
		'view_replace_str'       => [],
		// 默认跳转页面对应的模板文件
		'dispatch_success_tmpl'  => APP_PATH . 'tpl' . DS . 'jumpurl.html',
		'dispatch_error_tmpl'    => APP_PATH . 'tpl' . DS . 'jumpurl.html',

	    'cache' => [
		    'type'   => 'File',  // 驱动方式
		    'prefix' => '',  // 缓存前缀
		    'expire' => 0,  // 缓存有效期 0表示永久缓存
		    'level' =>3,
		],
		'SITE_NAME'          => 'izhimo 免费小说站',
		'SITE_URL'           => 'http://www.izhimo.com/',
		'PIC_URL'            =>'http://www.izhimo.com/',
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
		'IMG_WATER_PIC'   => __ROOT__ . 'public/water.png',
		'IMG_SIZES'   =>[
		          			['w'=>'120','h'=>'150']
				 		],
		'WWW_CATE_ALL' =>	[
	        ['name' => '玄幻奇幻', 'tag' => '玄幻|奇幻', 'id' => 3 ],
	        ['name' => '都市小说',  'tag' => '都市',  'id' => 4 ],
	        ['name' => '武侠修真',  'tag' => '武侠|修正|仙侠',  'id' => 6  ],
	        ['name' => '历史军事',  'tag' => '历史|军事', 'id' => 10   ],
	        ['name' => '女生言情', 'tag' => '言情',  'id' => 5 ],
	        ['name' => '经管励志', 'tag' => '经济|管理|励志',  'id' => 12 ],
	        ['name' => '法律教育', 'tag' => '法律|教育|心理',  'id' => 13 ],
	        ['name' => '文学名著', 'tag' => '文学|名著|古文|经典',  'id' => 7 ],
	        ['name' => '科幻小说', 'tag' => '科幻',  'id' => 8 ],
	        ['name' => '恐怖小说', 'tag' => '恐怖|悬疑|灵异',  'id' => 9]
	       // ['name' => "未分类",  'tag' => '*', 'id' =>1 ],
    	],
    	'url_html_suffix' =>'',
		'url_route_on'      => true,
		'url_route_must'=>  false,
	];
?>