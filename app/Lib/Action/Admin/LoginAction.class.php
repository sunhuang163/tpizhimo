<?php
if (!defined('THINK_PATH')) exit();

class LoginAction extends AllAction {
    protected $a_u = array();
	protected $m_web = array();

    public function  __construct(){
		//判断是否已经登录
	}

   public function index(){
    $this->login();
   }

   public function login(){
     //HTTP get
	 //HTTP POST
   }

   public function logout(){
   }
}