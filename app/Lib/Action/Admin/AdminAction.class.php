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