<?php
if (!defined('THINK_PATH')) exit();

class AllAction extends Action {
	protected $m_web = array();//global website information

    public function  __construct(){
	  parent::__construct();
      //load project basic information
	}

    //Log function
    protected function ulog( $uid , $name , $type = 'NF' ,$msg = 'Nothing'){
     $MLog = M('log');
     $Ldata = array();

	 $Ldata['uid'] = $uid;
	 $Ldata['uname'] = $name;
	 /* ... more data add here*/
	 $Ldata['ctime'] = time();

	 return $MLog->data( $Ldata )->add();
   }
}