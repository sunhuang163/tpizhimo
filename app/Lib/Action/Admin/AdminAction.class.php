<?php
/*--系统管理员操作--*/
class AdminAction extends BackAction {
    //main page
	public function index(){
	   $this->display();
	}

	public function setting(){
		echo "account setting";
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
	  $Mlog = M('syslog');
	  $wheres['said'] = array('eq',$this->a_u['uid']);
      $call = $Mlog->where( $wheres )->count("*");
      $pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
	  if( $p > $pall )
		  $p = $pall;
	  $limits = ($p-1)*$this->a_psize;
	  $limits.=','.($p*$this->a_psize);
	  $Ldata = $Mlog->where( $wheres )->limit( $limits )->select();
	  $url = U('/Admin/Admin/logindex',array('p'=>'{!page!}'));
      $pagestr = pagestr( $p , $pall , $url , $this->a_psize);

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