<?php
/**
 *
 *@author banfg56
 *@date 2015/1/30
 *@info 首页推荐管理
*/
class HotAction extends BackAction {

	public function index()
	{
       $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
	  if( $p< 1)
		  $p = 1;
	  $call = 0;
	  $pall = 1;
	  $limits ='';
	  $Ldata = NULL;
	  $wheres = array();
	  $wheres['ncid'] = array('eq',0);
	  $Dhot  = D('Hot');
      $call = $Dhot->where( $wheres )->count("*");
      $pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
	  if( $p > $pall )
		  $p = $pall;
	  $limits = ($p-1)*$this->a_psize;
	  $limits.=','.$this->a_psize;
	  $Ldata = $Dhot->where( $wheres )->order('ncid,ord ASC')->limit( $limits )->select();
	  $url = U('/Admin/Hot/index',array('p'=>'{!page!}'));
      $pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);
	  $this->assign('hotlist',$Ldata);
	  $this->assign('pagestr' , $pagestr);
	  $this->assign('call', $call);
	  $this->assign('pnow',$p);
	  $this->display();
	}

     //添加热门推荐
    public function add()
   {
    if( $this->isGet() )
    {
      $wheres = array();
	  $Dhot = M("Hot");

	}
	// POST add
	else
	{

	}
   }

   //删除热门推荐
   public  function delete()
   {
	 $rid = isset( $_REQUEST['rid']) ? (array)$_REQUEST['rid'] : NULL;
	 $res = array('rcode'=>-1,'msg'=>'服务器忙，请稍后再试');
	 if( !$rid ){
	  $res['msg'] = "提交参数错误";
	 }
	 else{
	  $wheres = array();
	  $Dhot = M("Recommend");
	  $du = array();
	  $du['ord'] = array('exp','ord-1');
	  foreach( $rid as $v){
		 $item = NULL;
		 $wheres['recommend_id'] = array('eq',$v);
		 $item = $Dhot->field('ncid,ord')->where( $wheres )->find();
		 $Dhot->where( $wheres )->delete();
		 if( $item ){
		  $whereup = array();
		  $whereup['ord'] = array('gt',$item['ord']);
		  $whereup['ncid'] = array('eq', $item['ncid']);
	      $Dhot->where( $whereup )->save( $wheres );
	    }
	  }
	  $res['rcode'] = 1;
	  $res['msg'] = "OK";
	 }
	 echo josn_encode( $res );
	 exit();
   }

   //二级分类下面的推荐
	public function cate()
   {
	 $ncid = isset( $_GET['ncid']) ? intval( $_GET['ncid']) : -1;
	 $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
	  if( $p< 1)
		  $p = 1;
	  $call = 0;
	  $pall = 1;
	  $limits ='';
	  $Ldata = NULL;
	  $wheres = array();
	  if( $ncid < 1 ) {
	  $wheres['ncid'] = array('neq',0);
	  }
	  else{
	   $wheres['ncid'] = array('eq' , $ncid);
	  }
	  $Dhot  = D('Hot');
      $call = $Dhot->where( $wheres )->count("*");
      $pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
	  if( $p > $pall )
		  $p = $pall;
	  $limits = ($p-1)*$this->a_psize;
	  $limits.=','.$this->a_psize;
	  $Ldata = $Dhot->where( $wheres )->order('ncid,ord ASC')->limit( $limits )->select();
	  $url = U('/Admin/Cate/index',array('p'=>'{!page!}','ncid'=>$ncid));
      $pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);
	  $this->assign('hotlist',$Ldata);
	  $this->assign('pagestr' , $pagestr);
	  $this->assign('call', $call);
	  $this->assign('pnow',$p);
	  $this->display();
   }

 }
?>