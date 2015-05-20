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
		if( cookie('_P_TAG') && !isset( $_REQUEST['p']))
		{
			$p = cookie('_P_TAG');
		}
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
		$Ldata = $Dtag->field("ih_tag.name,ih_tag.ctime,ih_tag.tid,ih_tag.tc")
						->order('ctime DESC')
						->limit( $limits )
						->select();
		$url = U('/Admin/Tag/index',array('p'=>'{!page!}'));
		cookie('_P_TAG' , $p );
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
    if( cookie('_P_TAGS') && !isset( $_REQUEST['p']))
    {
    	$p = cookie('_P_TAGS');
    }
    $Mtagview = D("TagsView");
    $wheres = array();
    $wheres['_string'] = '1=1';
    $wheres['tid'] = array('eq' , $tid );
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
	  $Ldata = $Mtagview->field("ih_novel.title,ih_nvoel.utime,ih_novel.uptxt,ih_novel.nid")
	  				    ->where( $wheres )
	  				    ->order('ctime DESC')
	  				    ->limit( $limits )
	  				    ->select();
	  $url = U('/Admin/Tag/tags',array('p'=>'{!page!}','tid'=>$tid));
	  cookie('_P_TAGS' , $p);
      $pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);
	  $this->assign('call' ,$call );
	  $this->assign('pnow' , $p);
	  $this->assign('dlist', $Ldata );
	  $this->assign('pagestr', $pagestr );
      $this->display();

  }
}
?>