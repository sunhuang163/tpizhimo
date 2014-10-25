<?php
if (!defined('THINK_PATH')) exit();

class BackAction extends AllAction {
    protected $a_u = array();

    public function  __construct(){
	  parent::__construct();
	  session_start();
      header('Cache-control:private,must-revalidate');
	  $this->auload( TRUE );
	}

     protected function  auload( $force = FALSE){
      $uinfo = array();
      $uinfo = session('_nvau');
	    if( count($uinfo) )
         {
		   $uinfo = authcode( $uinfo , "DECODE");
		    $this->a_u = $uinfo;
		   if( $force ){
		    $this->aupdate($this->a_u['uid']);
	 	  }//force
	    } //if
   }//function auload

   protected function aupdate( $uid ){
	 //load user
	//update  user infomation
	//update session
   }

    protected function  _aulogin( $force = FALSE ){
      if( $force )
	   $this->auload( $force );
	  if( isset( $this->a_u) && isset( $this->a_u['uid']) && $this->a_u['uid'])
		  return TRUE;
	  else
		  return FALSE;
	}
}