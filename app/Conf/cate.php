<?php
if (!defined('THINK_PATH')) exit();

return array(
		'WWW_CATE_HOME' => array(
		array('name'=>'首页','id'=>0,'url'=>"/"),
		array('name'=>'玄幻','id'=>2,'url'=>"xuanhuanqihuan"),
		array('name'=>'武侠','id'=>3,'url'=>"wuxia"),
		array('name'=>'都市','id'=>4,'url'=>"dushi" ),
		array('name'=>'历史','id'=>5,'url'=>"lishi" ),
		),
		'WWW_CATE_SUB' => array(
			array('name'=>'首页','id'=>0,'url'=>"/"),
			array('name'=>'玄幻','id'=>2,'url'=>"xuanhuanqihuan"),
			array('name'=>'武侠','id'=>3,'url'=>"wuxia"),
			array('name'=>'都市','id'=>4,'url'=>"dushi"),
			array('name'=>'历史','id'=>5,'url'=>"lishi"),
			),
		'WWW_CATE_ALL' =>array(
	        array('name' => '玄幻奇幻', 'tag' => '玄幻|奇幻', 'id' => 3 ),
	        array('name' => '都市小说',  'tag' => '都市',  'id' => 4 ),
	        array('name' => '武侠修真',  'tag' => '武侠|修正|仙侠',  'id' => 6  ),
	        array('name' => '历史军事',  'tag' => '历史|军事', 'id' => 10   ),
	        array('name' => '女生言情', 'tag' => '言情',  'id' => 5 ),
	        array('name' => '经管励志', 'tag' => '经济|管理|励志',  'id' => 12 ),
	        array('name' => '法律教育', 'tag' => '法律|教育|心理',  'id' => 13 ),
	        array('name' => '文学名著', 'tag' => '文学|名著|古文|经典',  'id' => 7 ),
	        array('name' => '科幻小说', 'tag' => '科幻',  'id' => 8 ),
	        array('name' => '恐怖小说', 'tag' => '恐怖|悬疑|灵异',  'id' => 9)
	       // array('name' => "未分类",  'tag' => '*', 'id' =>1 ),
    	),
	);
?>