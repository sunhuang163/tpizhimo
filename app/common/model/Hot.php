<?php
/*
  banfg56
  2015/2/4
  @@热门推荐列表小说
*/
namespace app\common\model;

class Hot extends \think\Model
{
     protected static $table = 'ih_recommend';

     const HOT_HOME_TXT = 'txt'; /**/
	 const HOT_HOME_PIC = 'pic'; /**/


	protected $_validate=array(
		  array('nid','require','小说不能为空',1),

	);

   protected function  _before_insert(&$data,$options)
  {
	 $wheres = [];
	 $wheres['ncid'] = ['eq',$data['ncid']];
	 if( $data['rtype'])
		 $wheres['rtype'] = ['eq' , $data['rtype']];
	 $cnt = self::where( $wheres )->max("ord");
	 if( $cnt === false || is_null($cnt) )
		 $cnt = 0;
	 else
		 $cnt++;
	 $data['ord'] = $cnt;
  }


}
?>