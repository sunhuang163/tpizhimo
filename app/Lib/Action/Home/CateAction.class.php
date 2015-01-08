<?php
/*----
   banfg56
   2015-1-8
   @@小说分类查看
----*/
class CateAction extends BaseAction {

	public function index()
   {
	   $this->display();
   }

    //二级分类的列表页
   public function  show()
  {
	 $this->display();
  }

}
?>