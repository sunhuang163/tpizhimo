<?php
/*--
  banfg56
  2014/11/30 星期日
  @@小说分类管理
--*/
class NclassAction extends BackAction {
    //main page
	public function index()
	{
	  $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
	  if( $p< 1)
		  $p = 1;
	  $call = 0;
	  $pall = 1;
	  $limits ='';
	  $Ldata = NULL;
	  $Dclass  = D('Nclass');
      $call = $Dclass->count("*");
      $pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
	  if( $p > $pall )
		  $p = $pall;
	  $limits = ($p-1)*$this->a_psize;
	  $limits.=','.$this->a_psize;
	  $Ldata = $Dclass->limit( $limits )->select();
	  $url = U('/Admin/Nclass/index',array('p'=>'{!page!}'));
      $pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);


	  $this->assign('call' ,$call );
	  $this->assign('pnow' , $p);
	  $this->assign('clist', $Ldata );
	  $this->assign('pagestr', $pagestr );
      $this->display();
	}

	 public function add()
	 {
        if( $this->isPost() )
	   {
		  $Mclass = D('Nclass');
		  $res = $Mclass->create();
		  if( $res ){
		   $Mclass->add();
		   $this->assign('jumpUrl',U('/Admin/Nclass/index'));
		   $this->success("分类添加成功");
		  }
		  else{
		   $this->assign("jumpUrl","javascript:history.go(-1);");
		   $this->error( $Mclass->getError() );
		  }
	  }
	 }

//目录的显示和隐藏
  public function ban()
{
  $Mnclass = D("Nclass");
  $ncid = isset( $_POST['ncid']) ? intval($_POST['ncid']):0;
  $state = isset( $_POST['state'] ) ? intval( $_POST['state']) :0;
  $res = array('rcode'=>0,'msg'=>'服务器忙，请稍后再试','data'=>NULL);
  $wheres = array();
  $wheres['ncid'] = array('eq', $ncid);
  $d = array();
  $d['state'] = 1==$state ? 0 : 1 ;
  $res['rcode'] = $Mnclass->data( $d )->where( $wheres )->save();
  if( $res['rcode'] ){
	  $res['msg']='OK';
	   $res['rcode'] = 1;
	  }
  else
	  $res['msg'] = '状态更新失败';

  echo json_encode( $res );
  exit();
}

	 //目录资料修改
     public function edit()
   {
     $Mnclass = D("Nclass");
	 $wheres = array();
     if( $this->isPost() )
	 {
		  $res = $Mnclass->Create();
		  if( $res ){
		   $Mnclass->save();
		   $this->assign('jumpUrl',U('/Admin/Nclass/index'));
		   $this->success("资料保存成功");
		  }
		  else{
		   $this->assign("jumpUrl","javascript:history.go(-1);");
		   $this->error("小说分类信息保存失败");
		  }
	 }
	 else
	{
     $id = isset( $_GET['id']) ? intval( $_GET['id']) : 0;
     $wheres['ncid'] = array('eq',$id);
	 $du = NULL;
	 $du = $Mnclass->where( $wheres )->find();
	 if( $du )
	 {
		  $this->assign('d',$du);
		  $this->display();
	 }
	 else{
	  $this->assign("jumpUrl" ,U('/Admin/Nclass/index'));
	  $this->error("编辑的小说分类不存在");
	 }
	}
   }
}
?>