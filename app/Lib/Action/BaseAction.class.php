<?php
if (!defined('THINK_PATH')) exit();

class BaseAction extends AllAction {
    protected $m_u = array();
	protected $m_isLogin = false;
	protected $m_path = "/static/";
	protected $m_path_public ="/public/";
    protected $m_cates = array(
		 array('name'=>'首页','id'=>1,'url'=>U('/Home/Cate/index','id'=>1)),
		 array('name'=>'玄幻','id'=>2,'url'=>U('/Home/Cate/index','id'=>2)),
		 array('name'=>'武侠','id'=>3,'url'=>U('/Home/Cate/index','id'=>3)),
		 array('name'=>'都市','id'=>4,'url'=>U('/Home/Cate/index','id'=>4)),
		 array('name'=>'历史','id'=>5,'url'=>U('/Home/Cate/index','id'=>5)),
	);

	   protected $m_cate_list = array(
		 array('name'=>'首页','id'=>1,'url'=>U('/Home/Cate/index','id'=>1)),
		 array('name'=>'玄幻','id'=>2,'url'=>U('/Home/Cate/index','id'=>2)),
		 array('name'=>'武侠','id'=>3,'url'=>U('/Home/Cate/index','id'=>3)),
		 array('name'=>'都市','id'=>4,'url'=>U('/Home/Cate/index','id'=>4)),
		 array('name'=>'历史','id'=>5,'url'=>U('/Home/Cate/index','id'=>5)),
		 array('name'=>'测试','id'=>6,'url'=>U('/Home/Cate/index','id'=>6)),
	);


   public function _initialize(){
	  parent::_initialize();
     // header('Cache-control:private,must-revalidate');
	  $module = MODULE_NAME;
      $action = ACTION_NAME;
	  $module = strtolower( $module );
	  $action = strtolower( $action );
	  $this->assign("ST_PATH",$this->m_path );
	  $this->assign("ST_PATH_PUBLIC",$this->m_path_public);
	  $this->assign("cate_index" , $this->m_cates );
	  $this->assign("cate_list" , $this->m_cate_list );
	  $this->assign("module" , $module);
	  $this->assign("action" , $action);
	}


}