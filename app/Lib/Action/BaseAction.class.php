<?php
if (!defined('THINK_PATH')) exit();

class BaseAction extends AllAction {
    protected $m_u = array();
	protected $m_isLogin = false;
    protected $m_cates = array(); 
    protected $m_cate_list = array(); 


   public function _initialize()
   {
   	  $webinfo = M('webinfo')->find();
   	  unset( $webinfo['extdata']);
	  parent::_initialize();
	  $module = MODULE_NAME;
      $action = ACTION_NAME;
	  $module = strtolower( $module );
	  $action = strtolower( $action );
	  $this->m_cates = C('WWW_CATE_HOME');
      $this->m_cate_list = C('WWW_CATE_SUB');
	  foreach( $this->m_cates as &$v)
	  {
	  	if( $v['id'] )
	  		$v['view'] = U('/Home/Cate/index',array('url'=>$v['url']));
	  	else
	  		$v['view'] = $v['url'];
	  }
	   foreach( $this->m_cate_list as &$v)
	  {
	  	if( $v['id'] )
	  		$v['view'] = U('/Home/Cate/index',array('url'=>$v['url']));
	  	else
	  	 	$v['view'] = $v['url'];
	  }
	  $this->assign("ST_PATH", C('ST_PATH') );
	  $this->assign("ST_PATH_PUBLIC", C('ST_PATH_PUBLIC'));
	  $this->assign("cate_index" , $this->m_cates );
	  $this->assign("cate_list" , $this->m_cate_list );
	  $this->assign("module" , $module);
	  $this->assign("action" , $action);
	  $this->assign("webinfo" , $webinfo );
	}


}