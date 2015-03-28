<?php
/*
  banfg56
  2015/2/4
  @@recommend novel
*/

class HotModel extends AdvModel {
     protected $tableName = 'recommend';

     const RECOMMEND_WITH_TXT = 'txt'; /**/
	 const RECOMMEND_WITH_PIC = 'pic'; /**/


	protected $_validate=array(
		  array('nid','require','小说不能为空',1),

	);

   protected function  _before_insert(&$data,$options)
  {
	 $wheres = array();
	 $wheres['ncid'] = array('eq',$data['ncid']);
	 if( $data['rtype'])
		 $wheres['rtype'] = array('eq' , $data['rtype']);
	 $cnt = $this->where( $wheres )->max("ord");
	 if( $cnt === false || is_null($cnt) )
		 $cnt = 0;
	 else
		 $cnt++;
	 $data['ord'] = $cnt;
  }


}
?>