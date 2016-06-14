<?php
namespace app\common\model;

class Syslog  extends \think\Model
{
	//自动验证
	protected $_validate=array(
	);
	//自动完成
	protected $_auto=array(
		array('ctime','time',1,'function'),
	);

	public static function logtype( $type )
	{
	  $ctype = strtoupper( $type );
	  $types  = array(
		       'LOGIN' => '用户登录',
		       'A_USER' => '用户管理',
	           );
     return isset( $types[$ctype]) ? $types[$ctype] : '';
	}

}

?>