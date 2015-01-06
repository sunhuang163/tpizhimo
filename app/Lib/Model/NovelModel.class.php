<?php
/*
  banfg56
  2014-11-28
  @@小说类
*/

class NovelModel extends RelationModel {

	//自动验证
	protected $_validate=array(
		  array('title','','该小说名称已经存在！',0,'unique',1),
		  array('title','require','小说标题不能为空!',1),
		  array('ncid','require','小说的分类不能为空!',1,'',1),

	);
	//自动完成
	protected $_auto=array(
		array('ctime','time',1,'function'),
	);


   protected function  _before_insert(&$data,$options)
  {
	$data['ctime'] = time();
	$data['utime'] = time();

   if( !$data['url'] ){
    $url = ff_pinyin( $data['title']);
	$wheres = array();
	$wheres['url'] = array('eq',$url);
	$Mnovel = M("Novel");
	$cnt = 0;
	$cnt = $Mnovel->where( $wheres )->count();
    if( $cnt )
		$url .= $cnt;
	$data['url'] = $url;
   }
   if( !$data['zimu']){
    $data['zimu'] = ff_letter_first( $data['title']);
   }
  }

  //分类文章增加,标签解析，分类计数增加,小说数据添加
  protected function _after_insert($data,$options)
 {
    $tags = isset( $_POST['tags']) ? trim( $_POST['tags']) :'';
    if( $tags )
    {
	 $Mtag = D("Tag");
	 $Mtag->parse($tags , $data['nid']);
	}
   $Mclass  = M("Nclass");
   $wheres = array();
   $wheres['ncid'] = array('eq',$data['ncid']);
   $d = array();
   $d['cn'] =array('exp','cn+1');
   $Mclass->where( $wheres )->save( $d );
   $Mndata = M("Ndata");
   $nd  = array();
   $nd['nid'] = $data['nid'];
   $Mndata->data( $nd )->add();

 }

 protected function _before_update(&$data,$options)
 {
 }

 protected function _after_update($data,$options)
{

}

}
?>