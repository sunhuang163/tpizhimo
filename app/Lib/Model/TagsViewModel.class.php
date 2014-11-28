<?php
class TagsViewModel extends ViewModel {
  	protected $viewFields = array (
		 'Tag'=>array('*','ctime'=>'tag_ctime'),
		 'Tagindex'=>array('*', '_on'=>'Tag.tid = Tagindex.tid'),
		 'Novel' => array('*','ctime'=>'novel_ctime','_on'=>'Tagindex.nid = Novel.nid'),
 	);
}
?>