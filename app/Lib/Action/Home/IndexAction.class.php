<?php
/*----
   banfg56
   2015-1-8
   @@网站首页
----*/
class IndexAction extends HomeAction {

	public function index()
   {
	  $this->display();
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