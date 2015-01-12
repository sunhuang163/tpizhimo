<?php
/*--小说采集操作--*/
class CaijiAction extends BackAction {
    //main page
	public function index()
	{
	  $Mcaiji = D("Caiji");
	  $rs = $Mcaiji->start();
	  $this->display();
	}

}
?>