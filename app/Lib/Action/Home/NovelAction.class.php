<?php
/*----
   banfg56
   2015-1-8
   @@小说编辑
----*/
class NovelAction extends BaseAction {

	public function index()
   {
	   $this->display();
   }


 //章节
 public function  show()
 {
	$this->display();
 }

 //阅读
 public function  read()
 {
  $this->display();
 }

}
?>