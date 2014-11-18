<?php
class WebinfoModel extends Model {
	 
	protected $_validate=array(
	);
	 
	protected $_auto=array(
		array('extdata','serialize',2,'function'),
	);


}
?>