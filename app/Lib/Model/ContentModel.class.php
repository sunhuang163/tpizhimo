<?php
/*
  banfg56
  2014-11-28
  @@小说章节具体内容
*/

class ContentModel extends AdvModel {

	//自动验证
	protected $_validate=array(
		  array('title','require','小说标题不能为空!',1),
		  array('content','require','小说内容不能为空',1),

	);
	//自动完成
	protected $_auto=array(
		array('content','hh_content',3,'callback'),
		array('ctime','time',1,'function'),
	);


  protected function hh_content( $str )
 {
	//采集内容中外链的处理，特殊字符的处理
	return remove_xss( $str );
 }

   protected function  _before_insert(&$data,$options)
  {
     $Mcnt = M("Content");
	 $wheres = array();
	 $wheres['nid'] = array('eq',$data['nid']);
	 $cnt = $Mcnt->where( $wheres )->max("ord");
	 if( !$cnt )
		 $cnt = 1;
	 else
		 $cnt++;
	 $data['ord'] = $cnt;
  }

  protected function _after_insert($data,$options)
 {
   $Mnovel = M("Novel");
   $dp = array();
   $wheres = array();
   $wheres['nid'] = array('eq',$data['nid']);
   $dp['uptxt'] = "更新至:".$data['title'];
   $dp['utime'] = time();
   $Mnovel->where( $wheres )->save( $dp );
 }

}
?>