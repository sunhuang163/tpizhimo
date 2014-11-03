<?php
class BackAction extends AllAction {
    protected $a_u = array();
	protected $a_psize = 15;

     public function _initialize(){
	   parent::_initialize();
	  session_start();
      header('Cache-control:private,must-revalidate');
	  $this->auload( );
	  if( !isset($this->a_u['uid']) || !$this->a_u['uid']){
		 $_SESSION['AdminLogin'] = 1;
		 header("Content-Type:text/html; charset=utf-8");
	    redirect(U('/admin/login/index','','','',TRUE), 5, '未登录，正在跳转到登录页面...');
	  }
	  $this->assign('au',$this->a_u);
	}

     protected function  auload( $force = FALSE){
      $uinfo = '';
      $uinfo = isset($_SESSION[C('U_AUTH_KEY')]) ? $_SESSION[C('U_AUTH_KEY')] : '';//session('_nvau');
	    if( count($uinfo) )
         {
		   $uinfos = authcode( $uinfo , "DECODE");
		   $arruinfo = unserialize( $uinfos );
		    $this->a_u = $arruinfo;
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
	  $dU['uid'] = $uid;
	  $srdu = serialize( $dU );
	  $uinfo = authcode( $srdu , 'ENCODE');
	  $this->a_u = $dU;
	  $_SESSION[C('U_AUTH_KEY')]=$uinfo;
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