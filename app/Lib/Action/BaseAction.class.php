<?php
if (!defined('THINK_PATH')) exit();

class BaseAction extends AllAction {
    protected $m_u = array();
	protected $m_isLogin = false;
	protected $m_path = "/static/";
	protected $m_path_public ="/public/";
    protected $m_cates = array(
		 array('name'=>'首页','id'=>0,'url'=>"/"),
		 array('name'=>'玄幻','id'=>2,'url'=>"xuanhuanqihuan"),
		 array('name'=>'武侠','id'=>3,'url'=>"wuxia"),
		 array('name'=>'都市','id'=>4,'url'=>"dushi" ),
		 array('name'=>'历史','id'=>5,'url'=>"lishi" ),
	);

	   protected $m_cate_list = array(
		 array('name'=>'首页','id'=>0,'url'=>"/"),
		 array('name'=>'玄幻','id'=>2,'url'=>"xuanhuanqihuan"),
		 array('name'=>'武侠','id'=>3,'url'=>"wuxia"),
		 array('name'=>'都市','id'=>4,'url'=>"dushi"),
		 array('name'=>'历史','id'=>5,'url'=>"lishi"),
	);


   public function _initialize(){
	  parent::_initialize();
     // header('Cache-control:private,must-revalidate');
	  $module = MODULE_NAME;
      $action = ACTION_NAME;
	  $module = strtolower( $module );
	  $action = strtolower( $action );
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
	  $this->assign("ST_PATH",$this->m_path );
	  $this->assign("ST_PATH_PUBLIC",$this->m_path_public);
	  $this->assign("cate_index" , $this->m_cates );
	  $this->assign("cate_list" , $this->m_cate_list );
	  $this->assign("module" , $module);
	  $this->assign("action" , $action);
	}


}