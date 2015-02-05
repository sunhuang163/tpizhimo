<?php
/*----
   banfg56
   2015-1-8
   @@网站首页
----*/
class IndexAction extends BaseAction {

	public function index()
   {
	 $Mhot = M("Recommend");
     $wheres = array();
	 $wheres['rtype'] = RecommendModel::RECOMMEND_WITH_TXT ;
	 $wheres['recommend.ncid'] = 0;

	 $dhot = array();
	 $drecommend = array();
	 $dupdate = array();

	 $dhot = $Mhot->field("novel.title,novel.author,novel.ncid,novel.pic,novel.url,novel.newurl,novel.ndesc")
		          ->join("left JOIN novel on novel.nid=recommend.nid")
		          ->order("ord ASC")
		          ->where( $wheres )
		          ->limit(5)
		          ->select();

     $this->assign("dhot" , $dhot);
	 $this->assign("");
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