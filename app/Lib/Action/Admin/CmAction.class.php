<?php
/*----网站评论管理----*/
class CmAction extends BackAction {

	public function index()
   {
	 $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
	  if( $p< 1)
		  $p = 1;
	  $call = 0;
	  $pall = 1;
	  $wheres = array();
	  $limits ='';
	  $Ldata = NULL;
	  $Dcm  = D('Cm');
	  $wheres['_string'] ='1=1';
      $call = $Dcm->where( $wheres )->count("*");
      $pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
	  if( $p > $pall )
		  $p = $pall;
	  $limits = ($p-1)*$this->a_psize;
	  $limits.=','.$this->a_psize;
	  $Ldata = $Dcm->where( $wheres )->order('ctime DESC')->limit( $limits )->select();
	  $url = U('/Admin/Cm/index',array('p'=>'{!page!}'));
      $pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);

	  $this->assign('call' ,$call );
	  $this->assign('pnow' , $p);
	  $this->assign('dlist', $Ldata );
	  $this->assign('pagestr', $pagestr );
      $this->display();
   }

}
?>