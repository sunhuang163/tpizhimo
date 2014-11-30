<?php
/*
  banfg56
  2014/11/30 星期日
  @@小说分类
*/

class NclassModel extends RelationModel {

	protected $_validate=array(
		  array('name','','该分类名称已经存在！',0,'unique',1),
		  array('url','','该访问地址已经存在！',0,'unique',1),
		  array('name','require','小说的名称不能为空!',1),

	);

	protected $_auto=array(
	);

	protected  function _before_insert(&$data,$options){
	  if( !isset( $data['url']) || !$data['url'])
	  {
	     $pinyin =ff_pinyin( $data['name']);
		  if( $pinyin )
	     {
		 $Mc = M('Nclass');
		 $wheres['url'] = array('eq',$pinyin );
		 $cnt = $Mc->where( $wheres )->count('*');
		 $url = $pinyin;
		 if( $cnt )
             $url .=$pinyin;
		 }//pinyin
	  }
	}

}
?>