<?php
class LoginAction extends AllAction {
    protected $a_u = array();
	protected $m_web = array();

      public function _initialize(){
	   parent::_initialize();
	  session_start();
      header('Cache-control:private,must-revalidate');
	  $uinfo = isset( $_SESSION['_nvau'] ) ? $_SESSION['_nvau'] : null;
	  $this->a_u = authcode( $uinfo , 'DECODE');
	  $login = FALSE;
	  if (!$_SESSION['AdminLogin']) {
			header("Content-Type:text/html; charset=utf-8");
			echo('请从后台管理入口登录。');
			exit();
		}
	  if( $this->a_u && isset( $this->a_u['uid']) &&   $this->a_u['uid'])
	  {
		  header("Content-Type:text/html; charset=utf-8");
		 redirect(U('Admin/index'), 5, '已经登录，正在跳转到后台...');
	  }
	}

   public function index(){
    $this->login();
   }

   public function login(){
     //HTTP get
	 //HTTP POST
	 $this->display('login');
   }

   public function check(){
   }

   public function logout(){
      if (isset($_SESSION['_nvau'])) {
			unset($_SESSION);
			session_destroy();
        }
		header("Content-Type:text/html; charset=utf-8");
		echo ('您已经退出网站管理后台，如需操作请重新登录！');
		exit();
   }
}
?>