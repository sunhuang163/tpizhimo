<?php
/*----
   banfg56
   2015-1-8
   @@网站首页
----*/
class IndexAction extends BaseAction {

	public function index()
   {
	   $this->display();
   }

   //排行榜
   public function rank()
  {
	$this->display();
  }

   //已经完结小说列表
  public function finish() {
   $this->display();
  }

  //最近更新小说
  public function recent()
 {
   $this->display();
 }

}
?>