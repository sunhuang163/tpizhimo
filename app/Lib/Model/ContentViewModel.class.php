<?php
class ContentViewModel extends ViewModel {
  	protected $viewFields = array (
		 'content'=>array('*','_type'=>"LEFT"),
		 'nclass'=>array('name'=>'catename', '_on'=>'nclass.ncid = content.ncid','_type'=>"LEFT"),
		 'nchapter' => array('*',"_on"=>"content.cpid=nchapter.cpid",'_type'=>"LEFT"),
		 'novel' => array('title'=>'novel_title','url'=>'novel_url','author'=>'novel_author','newurl'=>'novel_newurl','_on'=>'novel.nid = content.nid'),
 	);
}
?>