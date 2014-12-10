<?php
/**
  banfg56
  2014/11/27 星期四
  @@附件管理
*/
class AttAction extends BackAction {

	public function index(){
      $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
	  if( $p< 1)
		  $p = 1;
	  $call = 0;
	  $pall = 1;
	  $limits ='';
	  $Ldata = NULL;
	  $DAtt  = D('Att');
      $call = $DAtt->count("*");
      $pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
	  if( $p > $pall )
		  $p = $pall;
	  $limits = ($p-1)*$this->a_psize;
	  $limits.=','.$this->a_psize;
	  $Ldata = $DAtt->order('ctime DESC')->limit( $limits )->select();
	  $url = U('/Admin/Att/index',array('p'=>'{!page!}'));
      $pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);
	  $this->assign('attlist',$Ldata);
	  $this->assign('pagestr' , $pagestr);
	  $this->assign('call', $call);
	  $this->assign('pnow',$p);
	  $this->display();
	}



   //form 表单上传图片
    public function  upload()
   {
     $f = ff_upload('upfile');
	if( $f['rcode']){
      $this->assign('jumpUrl',U('/Admin/Att/index'));
	  $this->success('文件上传成功');
	}
	else
    {
     $this->assign('jumpUrl','javascript:history.go(-1);');
	 $this->error($f['msg']);
	}
   }

   //swfupload 上传文件
  public function swf()
 {
	  $admin_id = isset( $_POST['admin_id']) ? trim( $_POST['admin_id']): '';
	  $aid = 0;
	  $aid = authcode( $admin_id,"DECODE");
	  $res = array('rcode' => 0,'msg' => '服务器忙，请稍后再试');
     if( $aid != $this->a_u['uid'])
	 {
       $res['msg'] = "Permission Denied";
	   echo json_encode( $res );
	   exit();
	 }
	 else
	{
	  $f = array();
	  $f = ff_upload('swffile');
	  echo  json_encode( $f );
	  exit();
	}
 }

}
?>