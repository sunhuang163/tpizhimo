<?php
/*
  banfg56
  2014-11-27
  @@系统用户
*/
namespace app\common\model;

class Sysuser extends \think\Model
{
	//自动验证
	protected $_validate = [
		  ['name','','帐号名称已经存在！',0,'unique',1],
		  ['name','require','账户名称不能为空!',1],
		  ['email','email','邮箱格式不正确',1],
		  ['email','','该邮箱已经存在！',0,'unique',1],
		  ['psw','require','密码不能为空',1,'',1],
	];
	//自动完成
	protected $_auto = [
		array('ctime','time',1,'function'),
		array('mtime','time',2,'function'), //用户资料更新时间
	];


	protected function  _before_insert(&$data,$options)
	{
		$rand = mt_rand(55,293837);
		$psw = $data['psw'];
		$psw = md6($rand.$psw);
		$data['salt'] = $rand;
		$data['psw'] = $psw;
		$data['utime'] = time();
		$data['state'] = 1;
		$data['mtime'] = time();
		$data['ip'] = getip();
	}

}
?>