<?php
namespace app\common\model;

class Webinfo extends \think\Model
{

	protected $_validate=array(
		array('seotitle','require','SEO标题不能为空！'),
		array('seokey','require','SEO关键词不能为空！'),
		array('seodesc','require','SEO描述不能为空！')
	);

	protected $_auto=array(
		array('extdata','serialize',3,'function'),
	);


}
?>