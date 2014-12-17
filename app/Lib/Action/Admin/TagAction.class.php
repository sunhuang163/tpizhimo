<?php
/*----
  banfg56
  2014/11/23 星期日
  @小说标签管理
----*/
class TagAction extends BackAction {

	public function index()
   {
	  $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
	  if( $p< 1)
		  $p = 1;
	  $call = 0;
	  $pall = 1;
	  $limits ='';
	  $Ldata = NULL;
	  $Dtag  = D('Tag');
      $call = $Dtag->count("*");
      $pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
	  if( $p > $pall )
		  $p = $pall;
	  $limits = ($p-1)*$this->a_psize;
	  $limits.=','.$this->a_psize;
	  $Ldata = $Dtag->order('ctime DESC')->limit( $limits )->select();
	  $url = U('/Admin/Tag/index',array('p'=>'{!page!}'));
      $pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);
	  $this->assign('call' ,$call );
	  $this->assign('pnow' , $p);
	  $this->assign('dlist', $Ldata );
	  $this->assign('pagestr', $pagestr );
      $this->display();
   }

   public function tags()
  {
   $tid = isset( $_REQUEST['tid']) ? intval( $_REQUEST['tid']) : 0;
   $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
   $Mtagview = D("TagsView");
   $wheres = array();
   $wheres['_string'] = '1=1';
   	  if( $p< 1)
		  $p = 1;
	  $call = 0;
	  $pall = 1;
	  $limits ='';
	  $Ldata = NULL;
      $call = $Mtagview->where( $wheres )->count("*");
      $pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
	  if( $p > $pall )
		  $p = $pall;
	  $limits = ($p-1)*$this->a_psize;
	  $limits.=','.$this->a_psize;
	  $Ldata = $Mtagview->where( $wheres )->order('ctime DESC')->limit( $limits )->select();
	  $url = U('/Admin/Tag/tags',array('p'=>'{!page!}','tid'=>$tid));
      $pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);
	  $this->assign('call' ,$call );
	  $this->assign('pnow' , $p);
	  $this->assign('dlist', $Ldata );
	  $this->assign('pagestr', $pagestr );
      $this->display();

  }
}
?>