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
		
	  }
	  else
	  {
		$Mwebinfo = M('webinfo');
		$rsweb = $Mwebinfo->limit(1)->find();
		$dweb = array();
		$dweb= $rsweb;
		$dweb['extdata'] =  $rsweb['extdata'] ? unserialize( $rsweb['extdata']) : NULL;
		$this->assign('Dweb', $dweb);
		$this->display();
	  }
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
	public  function logdel(){
	 exit('del log ');
	}

    //日志条件查找
	public function logsearch(){
	}
}
?>