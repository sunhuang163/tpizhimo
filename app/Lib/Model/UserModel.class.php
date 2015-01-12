<?php
/*
  banfg56
  2015-1-12
  @@用户管理
*/

class UserModel extends AdvModel {

	protected $_validate=array(
		  array('name','','该分类名称已经存在！',0,'unique',1),
		  array('url','','该访问地址已经存在！',0,'unique',1),
		  array('name','require','小说的名称不能为空!',1),

	);

	protected $_auto=array(
		 array('cn','0'),
		 array('state','1'),
	);

	protected  function _before_insert(&$data,$options){

	}

}
?>