<?php
class TagModel extends RelationModel {

	protected $_validate=array(
      array('name','require','标签名称必需存在!'),
	);

	protected $_auto=array(
		array('ctime','time',1,'function'),
	);

    protected $_link = array(
       'tags'=> array(
       'mapping_type'=>HAS_MANY,
                    'class_name'=>'Tagindex',
                    'foreign_key'=>'tid',
                    'mapping_name'=>'tags',
                    'mapping_order'=>'ctime asc',
       ),

	);

    //根据字符串解析标签
	public function  parse($tags ,$nid  , $sep = "|"){
      $tags = trim( $tags );
	  $arrTag = array();
	  $arrTag = explode( '|',$tags );
	  $Mtag = M('Tag');
	  $Mtags = M('Tagindex');
	  $wheres = array();
	  $wheresti = array();
      foreach( $arrTag as $_v)
	  {
		if( $_v )
	  {
		$_v = strtolower( $_v );
        $wheres['name'] = array('eq',$_v);
		$dtag = array();
		$dtag = $Mtag->where( $wheres )->find();
		if( !$dtag ){
		 $dtag['name'] = $_v;
         $dtag['tc'] = 1;
		 $dtag['ctime'] = time();
		 $dtag['tid'] = $Mtag->data( $dtag )->add();
		 $dtindex = array();
		 $dtindex['ctime'] = time();
		 $dtindex['tid'] = $dtag['tid'];
		 $dtindex['nid'] = $nid;
		 $Mtags->data( $dtindex )->add();
		}
		else
	   {
		 $wheresti['nid'] = array('eq',$nid);
		 $wheresti['tid'] = $dtag['tid'];
		 $dindex = NULL;
		 $dindex = $Mtags->where( $wheresti )->find();
         if( !$dindex )
	    {
		   $dup = array();
		   $wheres['tid'] = array('eq',$dtag['tid']);
		   $dup['tc'] =array('exp','tc+1');
		   $dtindex = array();
		   $dtindex['ctime'] = time();
		   $dtindex['tid'] = $dtag['tid'];
		   $dtindex['nid'] = $nid;
		   $Mtags->data( $dtindex )->add();
		   $Mtag->data( $dup )->where( $wheres )->save();
		 } //if !$dindex
	   }// else
	 }// if
	}//foreach
 }
}