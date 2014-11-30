<?php
/*--
   banfg56
   2014/11/30 星期日
   @@小说操作类
--*/
class NovelAction extends BackAction {
    //main page
	public function index(){
	   $this->display();
	}

	public function add(){
	 if( !$this->isPost() ){
       $this->display();;
	 }
	 else{
	   // do the post add
	 }
	}

   //小说章节管理
	public function chapters()
   {
   }

   public function chapter_add()
   {
   }

  public function chapter_edit()
  {
  }
   //小说内容管理
   public function contents()
  {
  }

  public function content_add()
 {
 }

  public function content_edit()
  {
  }

}
?>