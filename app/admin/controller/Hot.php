<?php
/**
 *
 *@author banfg56
 *@date 2015/1/30
 *@info 首页推荐管理
*/
namespace app\admin\controller;

use app\common\controller\Base;

class Hot extends Base
{

	public function index()
	{
       $p  =  isset( $_REQUEST['p']) ?? 1;
	   $rtype  =  isset( $_GET['rtype']) ?? "txt";
	  if( $p< 1)
		  $p = 1;
	  $call = 0;
	  $pall = 1;
	  $limits ='';
	  $Ldata = NULL;
	  $wheres = [];
	  $wheres['re.ncid'] = array('eq',0);
	  if( $rtype)
      $wheres['rtype'] = array('eq' , $rtype);
	  $Dhot  = new \app\common\model\Hot;
      $call = $Dhot::alias('re')->where( $wheres )->count();
      $pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
	  if( $p > $pall )
		  $p = $pall;
	  $limits = ($p-1)*$this->a_psize;
	  $limits.=','.$this->a_psize;
	  $Ldata  =     $Dhot::alias('re')
	  					  ->field("re.*,nc.name,n.title,n.author,n.utime")
		                  ->join("novel n","n.nid=re.nid","LEFT")
		                  ->join("nclass nc","re.ncid=nc.ncid","LEFT")
		                  ->where( $wheres )
		                  ->order('re.ncid,re.ord ASC')
		                  ->limit( $limits )->select();

		$url = url('/admin/Hot/index',array('p'=>'{!page!}'));
		$pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);
		$this->assign('hotlist',$Ldata);
		$this->assign('pagestr' , $pagestr);
		$this->assign('call', $call);
		if( $rtype =='pic')
			$this->assign('pic' ,1 );
		else
		  $this->assign('txt' ,1);
	  	$this->assign('pnow',$p);
	  	return $this->fetch();
	}

    /**
	 添加热门推荐，只能是从小说列表页推荐
	*/
    public function add()
   {
    if( $this->isGet() )
	{
	}
	// POST add
	else
	{
	  $res = array('rcode'=>0,'msg'=>'Server Busy','data'=>null);
     	  $dhot = array();
	  $dhot = isset( $_POST['hot']) ? (array)$_POST['hot'] : array();
	  $Mdo = D("Hot"); //must use D to load defined Model
	  if( count($dhot) )
	 {
		$inc = 0;
          	foreach( $dhot as $v){
		   	 $item = array();
			$item['nid'] =  $v['nid'];
			$item['ncid'] = $v['ncid'];
			$item['rtype'] = isset( $v['rtype'])  ? trim( $v['rtype']) : "";
			$find = $Mdo->where( $item )->find();
			if( !$find ){
			 $inc++;
			 $Mdo->data( $item )->add();
			}//if
		  }//foreach
		  $res['rcode'] = $inc ? 1:0;
		  $res['msg']   = $inc ? "OK":"该小说已经推荐";
		  $res['data']  = $inc;
	  }
	  else{
	   $res['msg'] = "参数为空";
	  }
      echo json_encode( $res );
	  exit();
 	}
   }

   //删除热门推荐
   public  function delete()
   {
	 $rid = isset( $_POST['rid']) ?  intval($_POST['rid']) : NULL;
	 $res = array('rcode'=>-1,'msg'=>'服务器忙，请稍后再试');
	 if( !$rid ){
	  $res['msg'] = "提交参数错误";
	 }
	 else
	{
	  $wheres = [];
	  $Dhot = M("Recommend");
	  $du = array();
	  $du['ord'] = array('exp','ord-1');
		 $item = NULL;
		 $wheres['recommend_id'] = array('eq',$rid);
		 $item = $Dhot->field('rtype,ncid,ord')->where( $wheres )->find();
		 $Dhot->where( $wheres )->delete();
		 if( $item ){
		  $whereup = array();
		  $whereup['ord'] = array('gt',$item['ord']);
		  if( !$item['rtype'] )
		    $whereup['ncid'] = array('eq', $item['ncid']);
		  else
            $whereup['ncid'] = array('eq',0);
		  $whereup['rtype'] = array('eq' , $item['rtype']);
	      $Dhot->where( $whereup )->save( $wheres );
	    }
	  $res['rcode'] = 1;
	  $res['msg'] = "OK";
	 }
	 echo json_encode( $res );
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
		$wheres = [];
		if( $ncid > 0)
			$wheres['re.ncid'] = array('eq' , $ncid);
		else
			$wheres['re.ncid'] = array('neq' ,0 );
		$Dhot  = new \app\common\model\Hot;
		$call = $Dhot::alias('re')->where( $wheres )->count();
		$pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
		if( $p > $pall )
		  $p = $pall;
		$limits = ($p-1)*$this->a_psize;
		$limits.=','.$this->a_psize;
		$Ldata = $Dhot::alias('re')
	  					  ->field("re.*,nc.name,n.title,n.author,n.utime")
		                  ->join("novel n","n.nid=re.nid","LEFT")
		                  ->join("nclass nc","re.ncid=nc.ncid","LEFT")
		                  ->where( $wheres )
		                  ->order('re.ncid,re.ord ASC')
		                  ->limit( $limits )->select();

		$url = url('/admin/aate/index',array('p'=>'{!page!}','ncid'=>$ncid));
		$pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);
		$this->assign('hotlist',$Ldata);
		$this->assign('pagestr' , $pagestr);
		$this->assign('call', $call);
		$this->assign('pnow',$p);
		$this->assign("cate" , 1);
		return $this->fetch("index");
   }

 }
?>
