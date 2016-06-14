<?php
namespace app\admin\controller;

use app\common\controller\Base;

class Index extends Base
{

	public function index()
	{
		$logdata = \think\Db::name('syslog')->order('ctime','DESC')->limit( "8" )->select();

		$list = array();
		foreach( $logdata as $_v){
		  $_v['logtype'] = \app\common\model\Syslog::logtype( $_v['ctype']);
		  $list[] = $_v;
		}
		//统计数据处理
		$cnt = array();
		$cnt['novel'] = \think\Db::name("novel")->count();
		$cnt['cm'] = \think\Db::name("ucomment")->count();
		$cnt['tag'] = \think\Db::name("tag")->count();
		$cnt['att'] = \think\Db::name("att")->count();
		$this->assign("dcnt",$cnt);
		$this->assign('loglist' , $list);
		return $this->fetch();
	}
 }
?>