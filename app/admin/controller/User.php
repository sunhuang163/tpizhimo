<?php
/*--网站会员管理--*/
namespace app\admin\controller;

use app\common\controller\Base;

class User extends Base
{
    //main page
	public function index()
	{
		$this->assign("call",'');
		$this->assign("pnow", 0 );
		$this->assign("pagestr",'');
		$this->assign('nlist', [] );
	   return $this->fetch();
	}

}
?>