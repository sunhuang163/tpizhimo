<?php
class BackAction extends AllAction {
    protected $a_u = array();

     public function _initialize(){
	   parent::_initialize();
	  session_start();
      header('Cache-control:private,must-revalidate');
	  $this->auload( TRUE );
	  if( !isset($this->a_u['uid']) || !$this->a_u['uid']){
		 $_SESSION['AdminLogin'] = 1;
		 header("Content-Type:text/html; charset=utf-8");
	    redirect(U('/admin/login','','','',TRUE), 5, '未登录，正在跳转到登录页面...');
	  }
	}

     protected function  auload( $force = FALSE){
      $uinfo = array();
      $uinfo = isset( $_SESSION['_nvau']) ? $_SESSION['_nvau'] : NULL;//session('_nvau');
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
	 $Mau = M('sysuser');
	 $dU = NULL;
	 $wheres = array();
	 $wheres['said'] = array('eq' , $uid);
	 $dU = $Mau->where( $wheres )->find();
	 if( $dU )
	 {
	  $uinfo = '';
	  $uinfo = authcode( $dU , 'ENCODE');
	  $this->a_u = $dU;
	  $this->a_u['uid'] = $uid;
	  $_SESSION['_nvau'] = $uinfo;
	  return TRUE;
	 }
	 else
		 return FALSE;
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
?>