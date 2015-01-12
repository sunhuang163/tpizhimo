<?php
/*--小说采集操作--*/
class CaijiAction extends BackAction {
    //main page
	public function index()
	{
	  $this->display();
	}

	public function all()
   {
       $res = NULL;
       $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
       $Mcaiji = D("Caiji");
	   $res = $Mcaiji->start( $p );
	   if( $res )
	   {
	    $this->assign("jumpUrl",$res);
         $this->assign("jumpUrl",U('/Admin/Caiji/index'));
		$this->error("页面跳转");
		//$this->success("本页已经采集完成，跳转到第".++$p."页");
	   }
	   else if( is_null( $res)){
	    $this->assign("jumpUrl",U('/Admin/Caiji/index'));
		$this->error("采集发生错误");
	   }
	   else{
	    $this->assign("jumpUrl",U('/Admin/Caiji/index'));
		$this->success("采集已经完成");
	   }
	}

}
?>