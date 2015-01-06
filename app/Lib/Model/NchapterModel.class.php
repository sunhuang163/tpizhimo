<?php
/*
  banfg56
  2014-11-28
  @@小说章节
*/

class NchapterModel extends AdvModel {

	protected $_validate=array(
		  array('nid','require','章节所属的小说不能为空!',1),
		  array('title','require','小说的章节名称不能为空',1,'',1),

	);

	protected $_auto=array(
	);


   protected function  _before_insert(&$data,$options)
  {
     $Mchapter = M("Nchapter");
	 $wheres = array();
	 $wheres['nid'] = array('eq',$data['nid']);
	 $cnt = $Mchapter->where( $wheres )->max("ord");
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