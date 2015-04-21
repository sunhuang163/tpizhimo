<?php
/*----
   banfg56
   2015-1-8
   @@网站首页
----*/
class IndexAction extends BaseAction {

	public function index()
   {
	 $Mdo = D("Hot");
     $dhot = array();
	 $dpic = array();
	 $wheres = array();
	 $wheres['rtype'] = array('eq', HotModel::HOT_HOME_TXT);
     $wheres['ih_recommend.ncid'] = array('eq' , 0);
	 $dhot = $Mdo->field("ih_novel.*,ih_nclass.name as catename")
		         ->join("LEFT JOIN ih_novel on ih_novel.nid=ih_recommend.nid")
		         ->join("LEFT JOIN ih_nclass on ih_nclass.ncid=ih_recommend.ncid")
		         ->where( $wehres )
		         ->order("ih_recommend.ord ASC")
		         ->limit("5")
		         ->select();

	 $wheres['rtype'] = array('eq' , HotModel::HOT_HOME_PIC);
	 $dpic = $Mdo->field("ih_novel.*,ih_nclass.name as catename")
		         ->join("LEFT JOIN ih_novel on ih_novel.nid=ih_recommend.nid")
		         ->join("LEFT JOIN ih_nclass on ih_nclass.ncid=ih_recommend.ncid")
		         ->where( $wheres )
		         ->order("ih_recommend.ord ASC")
		         ->limit("4")
		         ->select();
	 $this->assign('dhot' , $dhot);
	 $this->assign('dpic' , $dpic );
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