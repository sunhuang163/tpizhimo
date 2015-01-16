<?php
class IndexAction extends BackAction {

	public function index()
	{
	  $Dlog = D('Syslog');
      $logdata = $Dlog->order('ctime DESC')->limit( 8 )->select();

	  $list = array();
      foreach( $logdata as $_v){
		  $_v['logtype'] = $Dlog->logtype( $_v['ctype']);
		  $list[] = $_v;
	  }
	   //统计数据处理
	   $cnt = array();
	   $cnt['novel'] = M("Novel")->count();
	   $cnt['cm'] = M("Ucomment")->count();
	   $cnt['tag'] = M("Tag")->count();
	   $cnt['att'] = M("Att")->count();
	   $this->assign("dcnt",$cnt);
       $this->assign('loglist' , $list);
	   $this->display();
	}
 }
?>