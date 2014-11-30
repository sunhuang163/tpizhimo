<?php
/*
  banfg56
  2014-11-28
  @@小说类
*/

class NovelModel extends RelationModel {

	//自动验证
	protected $_validate=array(
		  array('title','','该小说名称已经存在！',0,'unique',1),
		  array('title','require','小说标题不能为空!',1),
		  array('ncid','require','小说的分类不能为空!',1,'',1),

	);
	//自动完成
	protected $_auto=array(
		array('ctime','time',1,'function'),
	);


   protected function  _before_insert(&$data,$options)
  {
    //
  }

  protected function _after_insert($data,$options)
 {

 }

 protected function _before_update(&$data,$options)
 {
 }

 protected function _after_update($data,$options)
{

 }

}
?>