<?php
/*
  banfg56
  2014-11-28
  @@小说章节
*/
namespace app\common\model;

class Nchapter extends \think\Model
{

	protected $_validate=array(
		  array('nid','require','章节所属的小说不能为空!',1),
		  array('title','require','小说的章节名称不能为空',1,'',1),
	   	  array('title,nid', 'onecp', '该章节已经存在', 1,'callback', 1),

	);

	protected $_auto=array(
	);


   protected function  onecp( $data )
  {
	$wherecp = [];
	$wherecp['title'] = array('eq',$data['title']);
	$wherecp['nid'] = array('eq',$data['nid']);
	if( self::where($wherecp )->find())
		return FALSE;
	else
	return TRUE;

  }

   protected function  _before_insert(&$data,$options)
  {
	 $wheres = array();
	 $wheres['nid'] = array('eq',$data['nid']);
	 $cnt = self::where( $wheres )->max("ord");
	 if( !$cnt )
		 $cnt = 1;
	 else
		 $cnt++;
	 $data['ord'] = $cnt;
  }

  protected function _after_insert($data,$options){
  }

}
?>