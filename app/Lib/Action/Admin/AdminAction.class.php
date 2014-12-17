<?php
/*--系统管理员操作--*/
class AdminAction extends BackAction {
    //main page
	public function index(){
	   $this->display();
	}

	public function setting(){
	  if( $this->isPost())
	  {
        $DWeb = D('Webinfo');
        $rs = $DWeb->create();
		if( !$rs ){
		 $this->error( $DWeb->getError());
		}
		else{
		 if( isset( $_POST['id']) ) $DWeb->save();  //save id not work
		  else
			  $DWeb->add();
		 $this->assign('jumpUrl',U('/Admin/Admin/setting'));
         $this->success("网站配置信息更新成功");
		}
	  }
	  else
	  {
		$Mwebinfo = M('Webinfo');
		$rsweb = $Mwebinfo->limit(1)->find();
		$dweb = array();
		$dweb= $rsweb;
		$dweb['extdata'] =  $rsweb['extdata'] ? unserialize( $rsweb['extdata']) : NULL;
		$this->assign('Dweb', $dweb);
		$this->display();
	  }
	}

     //清理缓存
	public function clearcache(){
	 //do crear the cache
	 $this->assign("jumpUrl",U('/Admin/'));
     $this->success("缓存清理成功");
	}
    //日志列表
	public function  logindex(){
      $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
	  if( $p< 1)
		  $p = 1;
	  $call = 0;
	  $pall = 1;
	  $wheres = array();
	  $limits ='';
	  $Ldata = NULL;
	  $Dlog  = D('Syslog');
	  $wheres['said'] = array('eq',$this->a_u['uid']);
      $call = $Dlog->where( $wheres )->count("*");
      $pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
	  if( $p > $pall )
		  $p = $pall;
	  $limits = ($p-1)*$this->a_psize;
	  $limits.=','.$this->a_psize;
	  $Ldata = $Dlog->where( $wheres )->order('ctime DESC')->limit( $limits )->select();
	  $url = U('/Admin/Admin/logindex',array('p'=>'{!page!}'));
      $pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);


	  $list = array();
      foreach( $Ldata as $_v){
		  $_v['logtype'] = $Dlog->logtype( $_v['ctype']);
		  $list[] = $_v;
	  }
	  $this->assign('call' ,$call );
	  $this->assign('pnow' , $p);
	  $this->assign('loglist', $list );
	  $this->assign('pagestr', $pagestr );
      $this->display();
	}

    //日志删除
	public  function log_del(){
	  $ids = isset( $_POST['ids']) ? (array)$_POST['ids'] : NULL;
	  $res = array('rcode' => 0,'msg'=>'服务器忙，请稍后再试!');
	  $wheres = array();
	  if( !$ids ){
	   $res['msg'] = '参数错误';
	  }
	  else
	  {
	   $Mlog = M('syslog');
       $wheres['id'] = array('in',$ids);
	   if( $Mlog->where( $wheres)->delete() ){
	     $res['rcode'] =1;
		 $res['msg'] = '删除成功';
	   }
	   else{
	    $res['msg'] = '删除失败';
	   }
	  }
	  echo json_encode( $res  );
	  exit();
	}


    //用户管理
	public function users()
   {
	 $Mu = M('Sysuser');
     $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
     if( $p< 1)
		  $p = 1;
	  $call = 0;
	  $pall = 1;
	  $wheres = array();
	  $wheres['_string'] = '1=1';
	  $limits ='';
	  $Ldata = NULL;

	  $call = $Mu->where( $wheres )->count("*");
      $pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
	  if( $p > $pall )
		  $p = $pall;
	  $limits = ($p-1)*$this->a_psize;
	  $limits.=','.$this->a_psize;
	  $Ldata = $Mu->where( $wheres )->order('ctime DESC')->limit( $limits )->select();
	  $url = U('/Admin/Admin/user',array('p'=>'{!page!}'));
      $pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);

	  $this->assign('call',$call);
	  $this->assign('pnow',$p);
	  $this->assign('ulist',$Ldata);
	  $this->assign('pagestr',$pagestr);
	  $this->display();
   }

   //添加后台用户
   public function users_add(){
	  $Du = D('Sysuser');
	  $res = $Du->create();
	  if( !$res){
	   $this->assign('jumpUrl','javascript:history.go(-1);');
	   $this->error( $Du->getError());
	  }
	  else{
       $msg = '添加用户:'.$Du->name.'  UID:';
	  $uid=$Du->add();
       $msg.= $uid ;
	   salog('','','A_USER',$msg);
	   $this->assign('jumpUrl',U('/Admin/Admin/users'));
	   $this->success("用户添加成功");
	  }
   }

   //用户资料修改
   public function users_edit()
  {
	  if( $this->isPost() ){
         $Du = D('Sysuser');
		 $res = $Du->create();
		 if( !$res ){
		  $this->assign('jumpUrl','javascript:history.go(-1);');
		  $this->error( $Du->getError() );
		 }
		 else{
			 $msg = '修改用户信息：'.$Du->name.' UID:'.$Du->said;
		  $Du->save();
		   salog('','','A_USER',$msg);
		  $this->assign('jumpUrl',U('/Admin/Admin/users'));
          $this->success("用户信息更新成功");
		 }
	  }
	  else{
	   $id = isset( $_GET['id']) ? intval($_GET['id']) : 0;
	   $Mu = M('Sysuser');
	   $wheres = array();
	   $wheres['said'] = array('eq',$id);
	   $Du = $Mu->field('said,name,email,psw')->where( $wheres )->find();
	   $res = array('html'=>'','rcode'=>0,'msg'=>'服务器忙,请稍后再试!');
       if( !$Du ){
	    $res['msg'] = '用户不存在';
	   }
	    else if( ($this->a_u['uid'] ==$id ) || ($this->a_u['uid']==1))
	   {

		 $this->assign('du',$Du);
		 $res['html'] = $this->fetch('Admin:users_edit');
		 $res['rcode'] = 1;
	   }
	   else{
          	$res['msg'] = '没有权限修改用户的密码';
	   }
	   echo json_encode( $res );
	   exit();
	  }
  }

   //用户密码更改
   public function users_psw()
  {
    if( $this->isPost() )
    {
	 $said = isset( $_POST['said']) ?  $_POST['said'] : 0;
	 $salt =  isset( $_POST['salt']) ? $_POST['salt'] : mt_rand(55,293837);
	 $psw = isset( $_POST['psw']) ? trim( $_POST['psw']) :'';
	 $newpass = isset( $_POST['newpass']) ? trim( $_POST['newpass']) : '';
	 $newpass2 =  isset( $_POST['newpass2']) ? trim( $_POST['newpass2']) : '';
	 $res = array('rcode'=>0,'msg'=>'服务器忙，请稍候再试!');
     $Mu = M('Sysuser');
	 $wheres = array();
	 $wheres['said'] = array('eq',$said);
     $dU = $Mu->where( $where )->find();
	 if( !$dU ){
	  $res['msg'] = '用户不存在';
	 }
	 else
     {
       if( !$newpass || ( $newpass != $newpass2)){
	    $res['msg'] = '两次输入密码不一致';
	   }
	   else
	   {
         if( $this->a_u['uid'] == $said ){
           if( $dU['psw'] != md6($dU['salt'].$psw)){
		     $res['msg'] = '用户密码验证错误';
			 $res['rcode'] = -1;
		   }
		   else{
		    $res['rcode'] = 1;
		   }
		 }
		 else if( $res['rcode'] != -1)
	     {
		   $data= array();
		   $data['mtime'] = time();
		   $data['salt'] = $salt;
		   $data['psw'] =  md6($salt.$newpass);
           if( $Mu->data( $data )->where( $wheres )->save()){
		    $res['rcode'] =1 ;
			$res['msg'] = '用户密码修改成功';
			if( $this->a_u['uid'] == $said ){
			 $res['rcode'] = 2; //用户退出登录，需要重新登录
			}
			else{
			 $res['msg'] = '用户密码修改保存失败';
			}
		   }
		 } //自己修改自己的密码，需要密码验证
	   }
	 }
	 if( $res['rcode'])
     {
		  if( 2 == $res['rcode']){
		   $this->assign('jumpUrl',U('/Admin/Login/logout'));
		   $res['msg'] = '密码修改成功，马上退出，需要重新登录';
		  }
		  else{
			 $this->assign('jumpUrl',U('/Admin/Admin/users'));
             $res['msg'] = '密码修改成功';
	        }
		  $this->success( $res['msg']);
	 }
	 else{
	   $this->assign('jumpUrl','javascript:history.go(-1);');
	   $this->error( $res['msg']);
	 }
	}
	else
   {
	   $id = isset( $_GET['id']) ? intval($_GET['id']) : 0;
	   $Mu = M('Sysuser');
	   $wheres = array();
	   $wheres['said'] = array('eq',$id);
	   $Du = $Mu->field('said,name,salt')->where( $wheres )->find();
	   $res = array('html'=>'','rcode'=>0,'msg'=>'服务器忙,请稍后再试!');
       if( !$Du  ){
	    $res['msg'] = '用户不存在';
	   }
	    else if( ($this->a_u['uid'] ==$id ) || ($this->a_u['uid']==1))
	   {
	     $rand = mt_rand(55,293837);
		 $this->assign('rand',$rand);
	     $this->assign('du',$Du);
		$res['html'] = $this->fetch('Admin:users_psw');
		$res['rcode'] = 1;
	   }
	   else
		{
		    $res['msg'] = '没有权限修改用户的密码';
	   }
	   echo json_encode( $res );
	   exit();
   }
  }


  //用户状态修改
  public function users_ban(){
   $id =isset( $_POST['id'] ) ? intval( $_POST['id']) : 0;
   $state = isset( $_POST['state']) ? intval( $_POST['state']) : -1;
   $res = array('rcode'=>0, 'msg'=>'服务器忙，请稍候再试!');
   if( $state == -1 || $id==0){
    $res['msg'] = '提交参数错误';
   }
  else
  {
   $Mu = M('Sysuser');
   $dU = NULL;
   $wheres['said'] = array('eq', $id);
   $dU = $Mu->where( $wheres )->find();
   if( !$dU ){
    $res['msg'] = '请求的用户不存在';
   }
   else if( ($this->a_u['uid'] ==$id ) || ($this->a_u['uid']==1) ){
	$data = array();
	$data['state'] = !$state;
	if( $Mu->data( $data )->where( $wheres)->save() ){
	 $res['rcode'] = 1;
	 $res['msg'] = '用户状态更新成功';
	}
	else
		$res['msg'] = '用户状态更新失败';
   }
   else
   {
	 $res['msg'] = '用户权限不够!';
   }
  }
   echo json_encode( $res );
   exit();
  }

}
?>