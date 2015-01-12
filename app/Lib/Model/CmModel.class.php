<?php
/*
  banfg56
  2015-1-12
  @@评论管理
*/

class CmModel extends AdvModel {

    protected $tableName = 'ucomment';

	protected $_validate=array(
		  array('msg','require','评论的内容不能为空!',13),

	);

	protected $_auto=array(
		 array('isok','0'),
	);

	protected  function _before_insert(&$data,$options){
      $data['msg'] = remove_xss( $data['msg']);
	}

}
?>