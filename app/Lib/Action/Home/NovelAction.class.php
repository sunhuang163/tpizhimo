<?php
/**
 *  @Author   banfg56
 *  @Date     2015-1-8
 *  @Info     小说管理
 *  @File      NovelAction.class.php
 *
 **/
class NovelAction extends HomeAction 
{

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
		$tags = array();
		$wheret['nid'] = array('eq', $dnovel['nid']);
		$_tags = M('tagindex')->field("name")
							 ->join("LEFT JOIN ih_tag on ih_tag.tid=ih_tagindex.tid")
							 ->where( $wheret )
							 ->select();
		foreach( $_tags as $v)
		{
			$tags[] = $v['name'];
		}

	   if( $dnovel )
	   	{
		   $dnovel['lasturl'] = ff_novel_last( $dnovel['url'] , $dnovel['nid'] , $dnovel['new_url']);
	    }
	    $this->assign("tags" , $tags );
	    $this->assign("novel", $dnovel);
	    $this->display();
   }


	public function  show()
    {
       $Mcontent = M("Content");
       $wheres = array();
       $nid = isset( $_REQUEST['nid']) ? intval( $_REQUEST['nid'] ) : 1;

       $wheres['nid'] = array('eq' , $nid );
       $dcontent = $Mcontent->where( $wheres )->order("cpid ASC,ord ASC")->select();
       $this->assign("contents" , $dcontent );
	   $this->display();
    }

    public function  read()
   {
       $MCview = D("ContentView");
       $wheres  = array();
       $ncntid = isset( $_REQUEST['ncntid']) ? intval( $_REQUEST['ncntid']) : 1;
       $wheres['ncntid'] = array('eq' , $ncntid );
       $dcontent = $MCview->where( $wheres )->find();
       $this->assign('content' , $dcontent );
	   $this->display();
   }

}
?>