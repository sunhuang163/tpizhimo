<?php
/*
  banfg56
  2014-11-28
  @@小说章节具体内容
*/

class ContentModel extends AdvModel {

	//自动验证
	protected $_validate=array(
		  array('title','require','标题不能为空!',1),
		  array('content','require','内容不能为空',1),
		  array('title,cpid,nid', 'onecnt', '该章节内容已经存在', 1,'callback', 1),
	  );
	//自动完成
	protected $_auto=array(
		array('content','hh_content',3,'callback'),
	);



  protected function onecnt( $data )
 {
	$_wherescnt = array();
	$_wherescnt['title'] = array('eq',$data['title']);
	$_wherescnt['cpid'] = array('eq',$data['cpid']);
	$_wherescnt['nid'] = array('eq',$data['nid']);
    if( $this->where($_wherescnt)->count() )
        return FALSE;
    else
        return TRUE;
  }


  protected function hh_content( $str )
 {
	//采集内容中外链的处理，特殊字符的处理
	$str = h( $str );
	return remove_xss( $str );
 }

   protected function  _before_insert(&$data,$options)
  {
	 if( !$data['ctime'])
		 $data['ctime'] = time();
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