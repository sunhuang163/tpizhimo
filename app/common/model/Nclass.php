<?php
/*
  banfg56
  2014/11/30 星期日
  @@小说分类
*/
namespace app\common\model;


class Nclass extends \think\Model
{

	protected $_validate=array(
		  array('name','','该分类名称已经存在！',0,'unique',1),
		  array('url','','该访问地址已经存在！',0,'unique',1),
		  array('name','require','小说的名称不能为空!',1),

	);

	protected $_auto=array(
		 array('cn','0'),
		 array('state','1'),
	);

	public function  _getcache( $update = FALSE )
	{
		$listData = cache('_ffnovel/cate');
	   	if( !$listData || $update)
	   	{
	   		$cates = self::all( function( $query){  $query->order('ord','asc'); } );
	   		$_cates = array();
	   		foreach( $cates as $v)
	   		{
	   			$_cates[$v['ncid']] = $v;
	   		}
	   		cache('_ffnovel/cate', $_cates);
	   		$listData = $_cates;
	   	}
	   	return $listData;
	}

	public function _cache()
	{
   		$cates = self::all( function( $query){  $query->order('ord','asc'); } );
   		$_cates = array();
   		foreach( $cates as $v)
   		{
   			$_cates[$v['ncid']] = $v;
   		}
   		cache('_ffnovel/cate', $_cates);
	}

	protected  function _before_insert(&$data,$options)
	{
		$ord = 0;
		$ord = self::max("ord");
		if( is_null( $ord ))
		  $ord = 0;
		else{
		$ord = intval( $ord );
		$ord++;
		}
		$data['ord'] = $ord;
		if( !isset( $data['url']) || !$data['url'])
		{
		 $pinyin =ff_pinyin( $data['name']);
		  if( $pinyin )
		 {
		 	$cnt = self::where( 'url','eq', $pinyin )->count();
		 	$url = $pinyin;
		 	if( $cnt )
		    	$url .=$pinyin;
		   		$pinyin = $url ;
		 	}//pinyin
		 	$data['url'] = $pinyin;
		}
	}

	protected function _after_insert($data,$options)
	{
		$this->_cache();
	}

	protected function _before_update(&$data,$options)
	{
		if( !isset( $data['url']) || !$data['url'])
		{
			$pinyin =ff_pinyin( $data['name']);
		 	if( $pinyin )
		 	{
		 		$wheres['url'] = array('eq',$pinyin );
		 		$cnt =	self::where( 'url','eq', $pinyin )->count();
		 		$url = $pinyin;
		 		if( $cnt )
		     		$url .=$pinyin;
		   		$pinyin = $url ;
		 	}//pinyin
		 	$data['url'] = $pinyin;
		}
	}

	protected function _after_update($data,$options)
	{
		$this->_cache();
	}
}
?>