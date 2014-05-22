<?php
if (!defined('THINK_PATH')) exit();

class AlldexAction extends Action {
    protected $m_u = array();
	protected $m_isLogin = false;
	protected $m_web = array();
   
    public function  __construct(){
	  parent::__construct();
      //load project basic information
	}
} 