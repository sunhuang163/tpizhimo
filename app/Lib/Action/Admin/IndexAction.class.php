<?php
class IndexAction extends BackAction {
    //main page
	public function index()
	{
	  $Dlog = D('Syslog');
      $logdata = $Dlog->order('ctime DESC')->limit( 8 )->select();

	  $list = array();
      foreach( $logdata as $_v){
		  $_v['logtype'] = $Dlog->logtype( $_v['ctype']);
		  $list[] = $_v;
	  }
       $this->assign('loglist' , $list);
	   $this->display();
	}
 }
?>