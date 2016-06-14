<?php
/**
 *	@@网站首页
 *
 * @author banfg56
 * @date 01/08/2015
 */
namespace app\index\controller;

use app\common\controller\Home;

class Index extends Home
{

	public function index()
   {
	  return $this->fetch();
   }

	//排行榜
	public function rank()
	{
		$this->display();
	}

    //已经完结
	public function finish()
	{
		$this->display();
	}

  	//最近更新
	public function update()
	{
		$this->display();
	}

}
?>