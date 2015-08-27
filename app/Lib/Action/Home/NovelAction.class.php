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
	   $dnovel = $Mdo->field("ih_novel.*,ih_ndata.*,ih_nclass.name as cate_name,ih_nclass.url as cate_url")
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
	    $this->assign("novel", $dnovel );
	    $this->assign("ncid" , $dnovel['ncid'] );
	    $this->display();
   }


	public function  show()
    {
       $Mcontent = M("content");
       $wheres = $wherec = array();
       $nid = 0;
       $url = isset( $_GET['url']) ? trim( $_GET['url']) : "";

       $wheres['ih_novel.url'] = array('eq' , $url );
       $dnovel = M('novel')->field("ih_novel.*,ih_nclass.name as cate_name,ih_nclass.url as cate_url")
		             		->join("LEFT JOIN ih_nclass on ih_nclass.ncid =ih_novel.ncid")
		             		->where( $wheres )->find();
       if( $dnovel )
       {
       		$wherec['nid'] = array('eq' , $dnovel['nid']);
         	$dcontents = $Mcontent->where( $wherec )->order("cpid ASC,ord ASC")->select();
       }
       $this->assign("novel" , $dnovel );
       $this->assign("contents" , $dcontents );
       $this->assign("ncid" , $dcontents['ncid'] );
	   $this->display();
    }

    public function  read()
   {
       $Mdo = D("ContentView");
       $wheres  = array();
       $ncntid = isset( $_REQUEST['nid']) ? intval( $_REQUEST['nid']) : 1;
       $wheres['ncntid'] = array('eq' , $ncntid );
       $dcontent = $Mdo->where( $wheres )->find();
       $nextUrl = "";
       $preUrl = "";
       $novelUrl = "";
       if( $dcontent )
       {
       		$menu_url  = ff_novel_mulu( $dcontent['novel_url'] , $dcontent['nid'], $dcontent['ncid'] , $dcontent['novel_newurl'] );
       		$wherecnt = array();
       		$wherecnt['cpid'] = array( 'ELT' , $dcontent['cpid'] );
       		$wherecnt['nid'] = array( 'eq' , $dcontent['nid'] );
       		if( $dcontent['ord'] > 2  )
       		{
       			$wherecnt['ord'] = array('lt', $dcontent['ord'] );
       		}
       		$preCnt = M('Content')->where( $wherecnt )->order(" cpid desc , ord desc")->find();
       		if( $preCnt )
       		{
       			$preUrl = U('/Home/Novel/read' , array('nid' => $preCnt['ncntid'] ));
       		}
       		unset( $wherecnt['ord'] );
       		$wherecnt['cpid'] = array( 'EGT' , $dcontent['cpid'] );
       		$nextCnt = M('Content')->where( $wherecnt )->order(" cpid ASC , ord ASC")->find();
       		if( $nextCnt )
       		{
       			$preUrl = U('/Home/Novel/read' , array('nid' => $nextCnt['ncntid'] ));
       		}
       		$novelUrl = ff_novel_url( $dcontent['novel_url'], $dcontent['nid'], $dcontent['ncid'], $dcontent['novel_newurl'] );
       }
       $this->assign("novelUrl", $novelUrl );
       $this->assign("nextUrl", $nextUrl );
       $this->assign("preUrl", $preUrl );
       $this->assign("menu_url" , $menu_url );
       $this->assign('content' , $dcontent );
       $this->assign("ncid" , $dcontent['ncid'] );
	   $this->display();
   }

   //js 统计信息
   public function ts()
   {
        if( $this->isGet() )
        {
            exit("error");
        }
        else
        {
            $act = isset( $_POST['act'] ) ? trim( $_SERVER['act']) : '';
            $act = strtolower( $act );
            if( $act == 'view')
            {
                //
            }
            else if( $act == 'recomm')
            {
                //
            }
            exit("js update novel data ");
        }
   }
}

?>
