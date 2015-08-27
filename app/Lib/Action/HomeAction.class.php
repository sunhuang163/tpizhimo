<?php
if (!defined('THINK_PATH')) exit();

class HomeAction extends AllAction {
    protected $m_u = array();
	protected $m_isLogin = false;


	public function _initialize()
	{
		$webinfo = M('webinfo')->find();
		unset( $webinfo['extdata']);
		parent::_initialize();
		$module = MODULE_NAME;
		$action = ACTION_NAME;
		$module = strtolower( $module );
		$action = strtolower( $action );
		$menus = array();
		$MCate = D("Nclass");
		$listData = $MCate->_getcache();
		foreach( $listData as $v)
		{
			$item = array();
			$item['url'] = '/'.$v['url'].'/';
			$item['name'] = $v['name'];
			$item['id'] = $v['ncid'];
			$menus[] = $item;
		}
		unset( $listData );
		unset( $MCate );
		$this->assign("ST_PATH", C('ST_PATH') );
		$this->assign("ST_PATH_PUBLIC", C('ST_PATH_PUBLIC'));
		$this->assign("menus" , $menus );
		$this->assign('ncid' , 0 );
		$this->assign("module" , $module);
		$this->assign("action" , $action);
		$this->assign("webinfo" , $webinfo );
	}


}