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
	  $this->assign('loglist', $Ldata );
	  $this->assign('pagestr', $pagestr );
      $this->display();
	}

	 public function add(){
	 }

	 public function remove(){
	 }
}
?>