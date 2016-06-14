<?php
/*----网站评论管理----*/
namespace app\admin\controller;

use app\common\controller\Base;
class Cm extends Base
{

	public function index()
   {
	 $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
	  if( $p< 1)
		  $p = 1;
	  $call = 0;
	  $pall = 1;
	  $wheres = [];
	  $limits ='';
	  $Ldata = NULL;
	  $Dcm  = new \app\common\model\Cm;
      $call = $Dcm::where( $wheres )->count();
      $pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
	  if( $p > $pall )
		  $p = $pall;
	  $limits = ($p-1)*$this->a_psize;
	  $limits.=','.$this->a_psize;
	  $Ldata = $Dcm::where( $wheres )->order('ctime DESC')->limit( $limits )->select();
	  $url = url('/admin/Cm/index',array('p'=>'{!page!}'));
      $pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);

	  $this->assign('call' ,$call );
	  $this->assign('pnow' , $p);
	  $this->assign('dlist', $Ldata );
	  $this->assign('pagestr', $pagestr );
      return $this->fetch();
   }

}
?>