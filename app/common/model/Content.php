<?php
/*
  banfg56
  2014-11-28
  @@小说章节具体内容
*/
namespace app\common\model;

class Content extends \think\Model
{

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
	$_wherescnt = [];
	$_wherescnt['title'] = array('eq',$data['title']);
	$_wherescnt['cpid'] = array('eq',$data['cpid']);
	$_wherescnt['nid'] = array('eq',$data['nid']);
    if( self::where( $_wherescnt )->count() )
        return FALSE;
    else
        return TRUE;
  }


  protected function hh_content( $str )
 {
	//采集内容中外链的处理，特殊字符的处理
	//替换掉script中的内容
	$str = h( $str );
	return remove_xss( $str );
 }

   protected function  _before_insert(&$data,$options)
  {
	 if( !$data['ctime'])
		 $data['ctime'] = time();
	 $wheres = [];
	 $wheres['nid'] = array('eq',$data['nid']);
	 $cnt = self::where( $wheres )->max("ord");
	 if( !$cnt )
		 $cnt = 1;
	 else
		 $cnt++;
	 $data['ord'] = $cnt;
  }

  protected function _after_insert($data,$options)
 {
	$wheres = $dp = array[];
	$wheres['nid'] = array('eq',$data['nid']);
	$dp['uptxt'] = "更新至:".$data['title'];
	$dp['utime'] = time();
	\think\Db::name('novel')->where( $wheres )->update( $dp );
 }

}
?>