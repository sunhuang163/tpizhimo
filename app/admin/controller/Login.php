<?php
namespace app\admin\controller;

use app\common\controller\All;

class Login extends All
{
    protected $a_u = array();
	protected $m_web = array();

	public function _initialize()
	{
		parent::_initialize();
		session_start();
		header('Cache-control:private,must-revalidate');
		$uinfo = $_SESSION[config('U_AUTH_KEY')] ?? null;
		$strau = authcode( $uinfo , 'DECODE');
		$this->a_u = unserialize( $strau );
		$acname =  ACTION_NAME;
		$acname = strtolower( $acname );
		$login = FALSE;
		if ( isset( $_SESSION['_from_admin_login_'] ) && !$_SESSION['_from_admin_login_'])
		{
			header("Content-Type:text/html; charset=utf-8");
			echo('请从后台管理入口登录。');
			exit();
		}
		if( 'logout' != $acname )
		{
			if( $this->a_u && isset( $this->a_u['uid']) &&   $this->a_u['uid'])
			{
			  	header("Content-Type:text/html; charset=utf-8");
			 	$this->redirect(url('admin/index/index') );
			}
		}
	}

	public function index()
	{
		return $this->login();
	}

	public function login()
	{
		$remember = cookie('remenber');
		$name = '';
		if( $remember )
		{
			$loginstr =  authcode($remember , "DECODE");
			$arrlogin = unserialize( $loginstr );
			$name = isset( $arrlogin['name']) ? $arrlogin['name'] : '';
		}
		$this->assign('uname',$name);
		return $this->fetch("login");
	}

	public function saverify()
	{
		\app\common\model\User::caption("vcode");
	}


	public function check()
	{
		$M_sa = \think\Db::name('sysuser');
		$dsa = [];
		$dsa['name'] = isset( $_POST['uname']) ?  trim( $_POST['uname']) : '';
		$dsa['psw'] = isset( $_POST['psw']) ? trim( $_POST['psw']) : '';
		$dsa['vcode'] = isset( $_POST['vcode']) ? trim( $_POST['vcode']) : '';
		$vcode = $_SESSION['vcode'];
		if( !$dsa['vcode'] || $dsa['vcode']!=$vcode)
		{
			salog(-1,'','LOGIN','后台登录验证码输入错误');
			$this->assign('jumpUrl',url('admin/login/index'));
			return $this->error("验证码错误，请重新登录");
		}
		$dU = $M_sa->where( 'name','eq', $dsa['name'] )->find();
		if( !$dU)
		{
			salog(-1,'','LOGIN','后台登录用户名输入错误');
			$this->assign('jumpUrl',url('admin/login/login'));
			return $this->error("用户不存在,请确定用户名输入是否正确");
		}
		$psw = md6( $dU['salt'].$dsa['psw']);
		if( $dU['psw'] != $psw)
		{
			salog(-1,$dsa['name'],'LOGIN','后台登录密码错误');
			$this->assign('jumpUrl',url('admin/login/login'));
		    return $this->error("密码错误，请重新登录");
		}
		salog($dU['said'], $dsa['name'] ,'LOGIN','登录成功');
		$dU['uid'] = $dU['said'];
		if( !$dU['state'] )
		{
			header("Content-Type:text/html; charset=utf-8");
			echo('该用户已经被禁止登录，请联系管理员！');
			exit();
		}
		unset($dU['psw']);
		if( isset( $_POST['remember'])&& $_POST['remember'])
		{
		   $login= array('name'=>$dU['name'],'uid'=>$dU['said']);
		   $logstr = serialize( $login );
		   $loginstr =  authcode($logstr , "ENCODE");
		   cookie('remenber',$loginstr,60*60*24*5);
		}
		$srdu = serialize( $dU );
		$uinfo = authcode($srdu,"ENCODE");
		$_SESSION['vcode'] = NULL;
		$_SESSION[config('U_AUTH_KEY')] = $uinfo;
		$Unow = [];
		$Unow['utime'] = time();
		$Unow['ip'] = getip();
		$M_sa->where( 'said','eq', $dU['said'])->update( $Unow );
		$this->assign('jumpUrl',url('admin/index/index'));
		return $this->success("登录成功，正在跳转到首页");
	}

	public function logout()
	{
	  	salog('','','LOGIN','退出登录');
	  	if (isset( $_SESSION[config('U_AUTH_KEY')] ) && $_SESSION[config('U_AUTH_KEY')] )
	  	{
			unset($_SESSION);
			session_destroy();
	    }
		$_SESSION[config('U_AUTH_KEY')] = null;
		header("Content-Type:text/html; charset=utf-8");
		echo ('您已经退出网站管理后台，如需操作请重新登录！');
		exit();
	}
}
?>