<?php
/**
* 系统路由配置
*
* @author banfg56
* @date 04/06/2016
*/
if (!defined('THINK_PATH')) exit();

//
return array(
		'zimu/:zimu' =>'Cate/zimu?p=1',
		'zimu/:zimu_:p' =>  'Cate/zimu',
		'/^(none|falv|wenxue|lizhi|kehuan|kongbu|lishi|yanqing|wuxia|dushi|xuanhuanqihuan)$/i' => 'Home/Cate/index?url=:1',
		'/^(none|falv|wenxue|lizhi|kehuan|kongbu|lishi|yanqing|wuxia|dushi|xuanhuanqihuan)\/([A-Za-z0-9_]+)$/i' => 'Home/Novel/index?cate=:1&url=:2',
		'/^(none|falv|wenxue|lizhi|kehuan|kongbu|lishi|yanqing|wuxia|dushi|xuanhuanqihuan)\/([A-Za-z0-9_]+)\/menu$/i' => 'Home/Novel/show?cate=:1&url=:2',
		'/^(none|falv|wenxue|lizhi|kehuan|kongbu|lishi|yanqing|wuxia|dushi|xuanhuanqihuan)\/([A-Za-z0-9_]+)\/(\d+)$/i' => 'Home/Novel/read?cate=:1&url=:2&nid=:3',
	);
?>