<?php
/*--采集操作--*/
class CaijiAction extends BackAction {
   private $caiji_size = 5; //每次解析5条记录防止，请求超时

   //main page
   public function index()
  {
	 $novels  = F("_caiji/novel");
	 $lists = F("_caiji/list");
	 $this->assign("npage", $lists ? count($lists) : 0);
	 $this->assign("nnovel", $novels ? count($novels) : 0);
	 $this->display();
  }


  //del all cache content
  public function trash(){
    import("ORG.Io.Dir");
    $dir = new Dir;
	@unlink(DATA_PATH.'_caiji/list');
	@unlink(DATA_PATH.'_caiji/novel');
    if(file_exists(DATA_PATH."_caiji/novel/") && !$dir->isEmpty(DATA_PATH."_caiji/novel/")){$dir->del(DATA_PATH."_caiji/novel/");}
	if(file_exists(DATA_PATH."_caiji/list/") && !$dir->isEmpty(DATA_PATH."_caiji/list/")){$dir->del(DATA_PATH."_caiji/list/");}
	$this->assign("jumpUrl",U('/Admin/Caiji/index',array('t'=>time())));
	$this->success("采集缓存清空成功!");
  }

  //show list cache content
  public function showlist()
 {
   $Mcaiji = D("Caiji");
   $cp = $Mcaiji->getChapter("http://www.day66.com/xiaoshuo/45/45954/");
   var_dump( count( $cp['data']['cp']));
   var_dump( count($cp['data']['cnt']));
   exit();
   $this->display();
 }

 //show  novel ache content
 public function shownovel()
 {
  $this->display();
 }

  /**
   @采集从列表页开始解析，然后再是内容页面
  **/
	public function all()
   {
       $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
       $Mcaiji = D("Caiji");
	   $res = $Mcaiji->nList( $p );
       if( $res ){
		$list = F("_caiji/list");
		if(!$list) {
			$list = array();
		  F("_caiji/list",$list);
		}
		else if( $p == 1)
			$list = array();
	    $list[$p] = $p;
		F("_caiji/list",$list);
		/*bugfix 多层目录下面，子目录的创建会失败*/
		F("_caiji/list/".$p, array('d'=>$res,'t'=>time(),'p'=>$p));
	   }
	  if( count($res)){
          $this->assign("jumpUrl",U('/Admin/Caiji/all',array('p'=>($p+1))));
		  $this->success("第".$p."页采集完成，跳转到下一页");
	  }
	  else{
	    $this->assign("jumpUrl", U('/Admin/Caiji/index'));
		$this->success("采集完成");
	  }
   }

  /**
    @根据根据地址解析详情页面，每次更新更成功则更新章节信息
 **/
  public function novel()
 {
   $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
   $subpos = isset( $_REQUEST['subpos']) ? intval( $_REQUEST['subpos']) : 0;//根据分页大小来确定
   $Mcaiji = D("Caiji");
   $Mcp = D("Nchapter");
   $Mnovel = M("Novel");
   $plist = F("_caiji/list");
   $Novels = F("_caiji/novel");
   if( !$Novels) {
	   $Novels = array();
	 F("_caiji/novel",$Novels);
	}
    if( isset( $plist[$p]))
  {
    $nList = F("_caiji/list/".$p);
    $psize = count($nList['d']);
	for($ic = $subpos ;$ic <( $subpos + $this->caiji_size)  ; $ic++ ){
	 if( $ic <= $psize  && isset( $nList['d'][$ic]['url'])){
        $res = NULL;
		$res = $Mcaiji->getNovel( $nList['d'][$ic]['url']);
		if( $res['data'])
		{
           preg_match('#Book\/(.*)\.aspx#isU',$nList['d'][$ic]['url'],$match);
           $bookid = $match[1] ;
           $contents = array();
	       $contents = $Mcaiji->getChapter("http://www.day66.com/xiaoshuo/".substr($bookid,0,2)."/".$bookid."/");
           if( $contents['data']){
			 $contents['data']['nid'] = $res['data'];
			 $contents['data']['ncid'] = $res['ncid'];
			//插入章节信息
			foreach( $contents['data']['cp'] as $vp){
			 $wherecp = array();
			 $dcp = array();
			 $dcp['ncid'] = $res['ncid'];
			 $dcp['nid'] = $res['data'];
			 $dcp['title'] = $vp;
			 $rescp = $Mcp->create($dcp,3);
			 if( $rescp ) $Mcp->add();
			}//foreach 章节
			 $_Novels = array_flip( $Novels);
			 if( $_Novles[$res['data']])
				 $Novels[$_Novles[$res['data']]] = $res['data'];
				 else
		      $Novels[] = $res['data'];
			 F('_caiji/novel',$Novels);
			 F('_caiji/novel/'.$res['data'], $contents['data']);
		   }
		}
	 }
	}//foreach
   if( $ic > $psize ){
     $p++;
	 if( isset( $plist[$p]))
	  {
		$this->assign("jumpUrl",U('/Admin/Caiji/novel',array('p'=>$p,'subpos'=>0,'t'=>time())));
		$this->success("本页采集完成，跳转到下一页");
	  }
	  else{
	   $this->assign("jumpUrl",U('/Admin/Caiji/index'));
	   $this->success("采集完成,跳转到采集中心页面");
	  }
   }
   else
   {
	 $this->assign("jumpUrl",U('/Admin/Caiji/novel',array('p'=>$p,'subpos'=>$ic,'t'=>time())));
	 $this->success("第".$p."页，数据未采集完成".$ic."条 共有".$psize."条，继续采集");
   }

  }
  else{
   $this->assign("jumpUrl",U('/Admin/Caiji/index'));
   $this->error("地址错误");
  }
 }

 /**
  @根据保存地址开始采集
 **/
  public function content()
 {
   $Mcaiji = D("Caiji");
   $Novels = F("_caiji/novel");
   $Mchapter = D("Nchapter");
   $Dcnt = D("Content");
   $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 0;
   $cp = isset( $_REQUEST['cp']) ? intval( $_REQUEST['cp']) : 0;//具体章节
   $pos = isset( $_REQUEST['pos']) ? intval( $_REQUEST['pos']) : 0; //具体内容
   $res = "";
   $Novels = F("_caiji/novel");
  if( $Novels )
  {
    $Novel = F("_caiji/novel/".$Novels[$p]);
	if( $Novel )
   {
	 /* bug: case when there is no chapter for this novel*/
	 $cpcount = count( $Novel['cp']);
     if( $cp >= $cpcount )
	{
      $this->assign("jumpUrl",U('/Admin/Caiji/content',array('p'=>($p+1),'cp'=>0,'pos'=>0,'t'=>time())));
	   $this->success("本章节解析完成，跳转到下一条");
	}//if next novel
	else
	{
      $poscount = isset( $Novel['cnt'][$cp]) ? count( $Novel['cnt'][$cp]) : 0;
	  if( $pos >= $poscount )
	  {
          $this->assign("jumpUrl",U('/Admin/Caiji/content',array('p'=>$p,'cp'=>($cp+1),'pos'=>0,'t'=>time())));
	     $this->success("当前数据，本章节解析成功，跳转到下一章。共".$cpcount."章,当前".$cp);
	  } // if next chapter
	  else
	  {
		$wherecp = array();
		$wherecp['ncid'] = array('eq',$Novel['ncid']);
		$wherecp['nid'] = array('eq',$Novel['nid']);
		$wherecp['title'] = array('eq',$Novel['cp'][$cp]);
		$cpid = $Mchapter->where( $wherecp )->order("ord DESC")->getField("cpid");
		if( !$cpid )
	   {
		  $dcp = array();
		  $dcp['ncid']  = $Novel['ncid'];
		  $dcp['nid'] = $Novel['nid'];
		  $dcp['title'] = $Novel['cp'][$cp];
		  if( $Mchapter->create( $dcp , 3) )
		 {
		   $cpid = $Mchapter->add(); //if no chapter ,add it
		  }
		}
        $ic = $pos;
		for( $ic ; $ic<(5+$pos) ; $ic++)
		{
		 if( $Novel['cnt'][$cp][$ic])
		   {
            $dcontent          =   array();
			$dcontent['cpid']  =   $cpid;
			$dcontent['ncid']  =    $Novel['ncid'];
			$dcontent['nid']   =      $Novel['nid'];
			$dcontent['caijiurl'] = $Novel['cnt'][$cp][$ic]['url'];
            $dcontent['ctime'] = $Novel['cnt'][$cp][$ic]['ctime'];
			$dcontent['title'] = $Novel['cnt'][$cp][$ic]['title'];
			$ncntid = NULL;
			$wherecnt = array();
			$wherecnt['ncid'] =array('eq',$dcontent['ncid']);
			$wherecnt['cpid'] = array('eq',$dcontent['cpid']);
			$wherecnt['nid'] = array('eq',$dcontent['nid']);
			$wherecnt['title'] = array('eq',$dcontent['title']);
			/*bug here: Content Model auto create ,validate not work*/
			if(!$Dcnt->where( $wherecnt )->find())
		   {
			  $dcontent['content'] =$Mcaiji->getContent( $dcontent['caijiurl']);
			  if( $Dcnt->create( $dcontent , 3) )
			  $ncntid = $Dcnt->add(); //if no content add it
			}
		 }//if the content exists
		}//for
            if( $ic >= $poscount )
		{
			 $this->assign("jumpUrl",U('/Admin/Caiji/content',array('p'=>$p,'cp'=>($cp+1),'pos'=>0,'t'=>time())));
			 $this->success("本条数据解析完成,跳转到下一章，当前第".$cp."章 共".$cpcount."章");
		 }
		 else
		 {
		   $this->assign("jumpUrl",U('/Admin/Caiji/content',array('p'=>$p,'cp'=>$cp,'pos'=>$ic,'t'=>time())));
		   $this->success("本条数据正在解析中，当前第".$cp."章 第".$pos."条 共".$poscount."条");
		 } //else still in this chapter,next pos
	  } //if this chapter
	} //if this chapter
   }
	else
   {
     $this->assign("jumpUrl",U('/Admin/Caiji/index',array('t'=>time())));
	 $this->success("内容解析完成，跳转到首页");
    }//novel cache content don't exits
  }
  else
 {
    $this->assign("jumpUrl",U('/Admin/Caiji/index',array('t'=>time())));
    $this->error("缓存内容不存在,请重新解析");
  } //no novel list cache exits
 }

 /**
  @function picfix
  @access public
  @info 修正采集图片的错误地址
 **/
 public function picfix()
 {
 }

}
?>