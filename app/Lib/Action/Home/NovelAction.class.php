<?php
/*----
   banfg56
   2015-1-8
   @@小说编辑
----*/
class NovelAction extends BaseAction {

	public function index()
   {
	   $url      =	isset( $_GET['url']) ? trim( $_GET['url']) : "";
	   $wheres   =	array();
	   $Mdo      =	M("Novel");

       $wheres['ih_novel.url'] = array('eq' , $url);
	   $dnovel = null;
	   $dnovel = $Mdo->field("ih_novel.*,ih_ndata.*,ih_nclass.name as catename")
		             ->join("LEFT JOIN ih_nclass on ih_nclass.ncid =ih_novel.ncid")
		             ->join("LEFT JOIN ih_ndata  on ih_ndata.nid=ih_novel.nid")
		             ->where( $wheres )
		             ->find();
	   if( $dnovel )
	   {
		   $dnovel['lasturl'] = ff_novel_last( $dnovel['url'] , $dnovel['nid'] , $dnovel['new_url']);
	   }
	   $this->assign("novel", $dnovel);
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