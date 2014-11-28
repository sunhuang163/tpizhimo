<?php
/*
  banfg56
  2014-11-28
  @@小说类
*/

class NovelModel extends RelationModel {

	//自动验证
	protected $_validate=array(
		  array('name','','帐号名称已经存在！',0,'unique',1),
		  array('name','require','账户名称不能为空!',1),
		  array('email','email','邮箱格式不正确',1),
		  array('email','','该邮箱已经存在！',0,'unique',1),
		  array('psw','require','密码不能为空',1,'',1),

	);
	//自动完成
	protected $_auto=array(
		array('ctime','time',1,'function'),
		array('mtime','time',2,'function'), //用户资料更新时间
	);


   protected function  _before_insert(&$data,$options)
  {
     //
  }

}
?>