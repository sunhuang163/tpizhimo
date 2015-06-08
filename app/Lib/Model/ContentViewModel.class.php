<?php
class TagsViewModel extends ViewModel {
  	protected $viewFields = array (
		 'content'=>array('*','_type'=>"LEFT"),
		 'nclass'=>array('*', '_on'=>'nclass.ncid = content.ncid','_type'=>"LEFT"),
		 'nchapter' => array('*',"_on"=>"content.cpid=nchapter.cpid",'_type'=>"LEFT"),
		 'novel' => array('*','_on'=>'novel.nid = content.nid'),
 	);
}
?>