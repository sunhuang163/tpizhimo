<?php
namespace app\common\controller;
use think\Controller;

class All extends Controller
{
	protected $m_web = array();//global website information
	protected $m_fpath = '/static/';
	protected $m_apath = '/public/';

    public function _initialize()
    {
	   header("Content-Type:text/html; charset=utf-8");
	   //load webinfo
	   $this->assign('STATIC_PATH', $this->m_fpath);
	   $this->assign('ADMIN_PATH' , $this->m_apath);
	   $this->assign('waitSecond',5);
	   $this->assign('website_name','i织墨文学爱好者站');
    }

    public function isGet()
    {
    	return IS_GET;
    }

    public function isPost()
    {
    	return IS_POST;
    }
}

?>