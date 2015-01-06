<?php
class TagsViewModel extends ViewModel {
  	protected $viewFields = array (
		 'tag'=>array('*','ctime'=>'tag_ctime'),
		 'tagindex'=>array('*', '_on'=>'Tag.tid = Tagindex.tid'),
		 'novel' => array('*','ctime'=>'novel_ctime','_on'=>'Tagindex.nid = Novel.nid'),
 	);
}
?>