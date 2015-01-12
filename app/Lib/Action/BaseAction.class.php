<?php
if (!defined('THINK_PATH')) exit();

class BaseAction extends AllAction {
    protected $m_u = array();
	protected $m_isLogin = false;
	protected $m_path = "/static/";
	protected $m_path_public ="/public/";

   public function _initialize(){
	  parent::_initialize();
      header('Cache-control:private,must-revalidate');
	  $this->_uload();
	  $this->assign("ST_PATH",$this->m_path );
	  $this->assign("ST_PATH_PUBLIC",$this->m_path_public);
	}

   protected function  _uload( $forece = FALSE){
    /*  $uinfo = session('_nvu');
	 if( $uinfo )
	 {
		 $uinfo = authcode( $uinfo , "DECODE");
		 $this->m_u = $uinfo;
		 $this->m_isLogin = TRUE;
		 if( $forece ){
		   $this->_update($this->m_u['uid']);
		 }
	 } */
   }

   protected function _update( $uid ){
    //load user
	//update  user infomation
	//update session
   }

   public function _Login()
  {
	  $this->m_isLogin =  isset( $this->m_u) && isset( $this->m_u['uid'] ) ? TRUE : FALSE;
	  return $this->m_isLogin;
   }


}