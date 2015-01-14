<?php
/*--采集操作--*/
class CaijiAction extends BackAction {
   private $caiji_size = 5; //每次解析5条记录防止，请求超时

    //main page
	public function index()
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
		if(!$list)
			$list = array();
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
   if( !$Novels)
	   $Novels = array();
    if( isset( $plist[$p]))
  {
    $nList = F("_caiji/list/".$p);
    $psize = count($nList['d']);
	F("_caiji/novel","fff");
	F("_caiji/novel",NULL);
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
   $Mcontent = D("Content");
   $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 0;
   $cp = isset( $_REQUEST['cp']) ? intval( $_REQUEST['cp']) : 0;//具体章节
   $pos = isset( $_REQUEST['pos']) ? intval( $_REQUEST['pos']) : 0; //具体内容
   $res = "";
   $Novels = F("_caiji/novel");
   $fnovel = "";
   if( !$Novels )
  {
     $this->assign("jumpUrl",U('/Admin/Caiji/index'));
	 $this->error("没有内容需要解析");
  }
  else
  {
    if( isset($Novels[$p]) && F('_caiji/novel/'.$Novels[$p]))
	{
       $dcnt = F('_caiji/novel/'.$Novels[$p]);
	  if( $cp < count( $dcnt['cp']) || 0 == count($dcnt['cp']))
	  {
        $wherecp = array();
	    $wherecp['title'] = array('eq', $dcnt['cp'][$cp] ? isset($dcnt['cp'][$cp]) : "");
	    $wherecp['nid'] = $dcnt['nid'];
	    $dcp = $Mchapter->field("cpid")->where( $wherecp )->find();
	    $cpid =  isset( $dcp['cpid']) ? $dcp['cpid'] : NULL;
		 if( !$cpid )
	    {
          $dcp = array();
		  $dcp['ncid'] = $dcnt['ncid'];
		  $dcp['nid'] = $dcnt['nid'];
		  $dcp['title'] = "正文";
		  $Mchapter->create( $dcp ,3);
		  $cpid = $Mchapter->add();
	    }
	    else
		{
	       unset( $wherecp['title']);
		   $dcp = $Mnovel->field("cpid")->where( $wherecp )->find();
		   $cpid = isset( $dcp['cpid']) ? $dcp['cpid'] : NULL;
	    }
        if( $pos < count($dcnt['cnt'][$cp]))
		{
			$ic = $pos;
			//每次采集5条数据，防止采集请求超时
			for( $ic ;$ic<5;$ic++){
			   if( $ic < count($dcnt['cnt'][$cp])){
			     $dcontent = array();
				 $dcontent['nid'] = $dcnt['nid'];
				 $dcontent['ncid'] = $dcnt['ncid'];
				 $dcontent['cpid'] = $cpid;
				 $dcontent['caijiurl'] = $dcnt['cnt'][$cp][$ic]['url'];
				 $dcontent['title'] = $dcnt['cnt'][$cp][$ic]['title'];
				 $dcontent['ctime'] = strtotime( $dcnt['cnt'][$cp][$ic]['ctime'] );
				 $dcontent['content'] = $Mcaiji->getContent( $dcnt['cnt'][$cp][$ic]['url']);
				 if( $Mcontent->create( $dcontent,3)){
				   $Mcontent->add();
				 }//if
			   }//for
			   if( $ic >= count($dcnt['cnt'][$cp]))
			   {
			     //该章节内容采集完成，跳转到下一章
				 $cp++;
				  $this->assign("jumpUrl",U('/Admin/Caiji/content',array('p'=>$p,'cp'=>$cp,'pos'=>0,'t'=>time())));
		          $this->success("该章节内容更新成功，跳转到下一章");
			   }
			   else
				{
                 $pos = $ic;
                 $this->assign("jumpUrl",U('/Admin/Caiji/content',array('p'=>$p,'cp'=>$cp,'pos'=>$pos,'t'=>time())));
				 $this->success("采集中，当前第".$p."页 第:".$cp."章 第".$pos."条数据，循环一下条数据");
			   }
			}//for
		}//if 数据存在，不需要翻页，和跳转到下一章
		else
		{
          //跳转到本小说的下一个章节
		  $cp++;
		  $this->assign("jumpUrl",U('/Admin/Caiji/content',array('p'=>$p,'cp'=>$cp,'pos'=>0,'t'=>time())));
		  $this->success("该章节内容更新成功，跳转到下一章");
		}
	   } //未匹配到章节信息
	   else if( $cp > count($dcnt['cp']))
		{
	     //跳转到下一章
          $p++;
           if( isset($Novels[$p]) ){
           $this->assgin("jumpUrl",U('/Admin/Caiji/content',array('p'=>$p,'cp'=>0,'pos'=>0,'t'=>time())));
           $this->success("该章节内容更新成功，跳转到下一章");
		}
		else
		{
		 $this->assign("jumpUrl",U('/Admin/Caiji/index'));
		 $this->success("章节内容更新成功");
		}
      } //在章节的内容之后
	  else
	  {
		//下一条记录
		$p++;
        if( isset($Novels[$p]) )
		{
           $this->assgin("jumpUrl",U('/Admin/Caiji/content',array('p'=>$p,'cp'=>0,'pos'=>0,'t'=>time())));
		   $this->success("更新下一章节");
		}
		else
		{
		 $this->assign("jumpUrl",U('/Admin/Caiji/index'));
		 $this->success("章节内容更新成功");
		}
	  }
	}
	else
   {
      $this->assgin("jumpUrl",U('/Admin/Caiji/index'));
	  $this->success("小说章节内容更新成功");
	}
  }//else Novels exists
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