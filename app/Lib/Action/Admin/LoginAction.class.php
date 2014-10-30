<?php
class LoginAction extends AllAction {
    protected $a_u = array();
	protected $m_web = array();

      public function _initialize(){
	   parent::_initialize();
	  session_start();
      header('Cache-control:private,must-revalidate');
	  $uinfo = isset($_SESSION[C('U_AUTH_KEY')]) ? $_SESSION[C('U_AUTH_KEY')] : null;
	  $strau = authcode( $uinfo , 'DECODE');
	  $this->a_u = unserialize( $strau );
	  $login = FALSE;
	  if (!$_SESSION['AdminLogin']) {
			header("Content-Type:text/html; charset=utf-8");
			echo('请从后台管理入口登录。');
			exit();
		}
	 if( 'logout' != strtolower( ACTION_NAME ))
	{
	  if( $this->a_u && isset( $this->a_u['uid']) &&   $this->a_u['uid'])
	  {
		  header("Content-Type:text/html; charset=utf-8");
		 redirect(U('/Admin/Index/index'), 5, '已经登录，正在跳转到后台...');
	  }
	 }
	}

   public function index(){
    $this->login();
   }

   public function login(){
	 $this->display('login');
   }

  public function saverify(){
     $width = '80';
     $height = '30';
    $characters = 5;

    $set_font = APP_PATH.'font/2.ttf';

    $code = $this->_vcode($characters);
   /* font size will be 55% of the image height */
    $font_size = $height * 0.55;
    $image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
    /* set the colors */
    $background_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 41, 85, 68);
    $noise_color = imagecolorallocate($image, 122, 192, 66);
    /* generate random dots in background */
    for ($i = 0; $i < ($width * $height) / 3; $i++) {
    imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 1, 1, $noise_color);
    }
   /* generate random lines in background */
   for ($i = 0; $i < ($width * $height) / 150; $i++) {
   imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $noise_color);
   }
   /* create textbox and add text */
   $textbox = imagettfbbox($font_size, 0, $set_font, $code) or die('Error in imagettfbbox function');
   $x = ($width - $textbox[4]) / 2;
   $y = ($height - $textbox[5]) / 2;
   imagettftext($image, $font_size, 0, $x, $y, $text_color, $set_font, $code) or die('Error in imagettftext function');
   Header("Content-type: image/jpeg");
   Imagejpeg($image);                    //生成png格式
   Imagedestroy($image);
   $_SESSION['vcode'] = $code;
   }

 protected function _vcode($characters = 4){
     $possible = '123456789abcdfghjkmnpqrstvwxyz';
     $code = '';
     $i = 0;
    while ($i < $characters) {
     $code .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
     $i++;
    }
    return $code;
   }

   public function check(){
	$M_sa = M('sysuser');
	$dsa = array();
	$dsa['name'] = isset( $_POST['uname']) ?  trim( $_POST['uname']) : '';
	$dsa['psw'] = isset( $_POST['psw']) ? trim( $_POST['psw']) : '';
	$dsa['vcode'] = isset( $_POST['vcode']) ? trim( $_POST['vcode']) : '';
	$vcode = $_SESSION['vcode'];
	if( !$dsa['vcode'] || $dsa['vcode']!=$vcode){
	 salog(-1,'','LOGIN','后台登录验证码输入错误');
	 $this->assign('jumpUrl',U('/Admin/Login/index'));
	 $this->error("验证码错误，请重新登录");
	}
	$wheres = array();
	$wheres['name'] = array('eq', $dsa['name']);
	$dU = $M_sa->where( $wheres )->find();
    if( !$dU){
	 salog(-1,'','LOGIN','后台登录用户名输入错误');
	 $this->assign('jumpUrl',U('/Admin/Login/login'));
	 $this->error("用户不存在,请确定用户名输入是否正确");
	}
	$psw = md6( $dU['salt'].$dsa['psw']);
	if( $dU['psw'] != $psw){
	   salog(-1,$dsa['name'],'LOGIN','后台登录密码错误');
	   $this->assign('jumpUrl',U('/Admin/Login/login'));
	    $this->error("密码错误，请重新登录");
	}
     salog($dU['said'], $dsa['name'] ,'LOGIN','登录成功');
	 $dU['uid'] = $dU['said'];
	 $srdu = serialize( $dU );
	 $uinfo = authcode($srdu,"ENCODE");
	 $_SESSION['vcode'] = NULL;
	 $_SESSION[C('U_AUTH_KEY')] = $uinfo;
	 $wheres = array();
	 $Unow = array();
	 $Unow['ctime'] = time();
	 $Unow['mtime'] = time();
	 $Unow['utime'] = time();
	 $Unow['ip'] = ip2long(getip());
	 $wheres['said'] = array('eq',$dU['said']);
	 $M_sa->data( $Unow )->where( $wheres )->save();
	 $this->assign('jumpUrl',U('/Admin/Index/Index'));
	 $this->success("登录成功，正在跳转到首页");
   }

   public function logout(){

      if ( $_SESSION[C('U_AUTH_KEY')] ) {
			unset($_SESSION);
			session_destroy();
        }
		$_SESSION[C('U_AUTH_KEY')] = null;
		header("Content-Type:text/html; charset=utf-8");
		echo ('您已经退出网站管理后台，如需操作请重新登录！');
		exit();
   }
}
?>