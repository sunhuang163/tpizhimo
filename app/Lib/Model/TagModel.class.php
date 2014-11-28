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
}
?>