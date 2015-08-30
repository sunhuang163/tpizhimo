<?php
/*--
   banfg56
   2014/11/30 星期日
   @@小说操作类
--*/
class NovelAction extends BaseAction {
	public function index()
	{
		$Mnclass = D("Nclass");
		$cates  = array();
		$cates = $Mnclass->field("ncid,name")->select();

		/*search condition*/
		$ncid = isset( $_GET['ncid']) ? intval( $_GET['ncid']) : -1;
		$author = isset( $_GET['author']) ? trim( $_GET['author']) : "";
		$title = isset( $_GET['title']) ? trim( $_GET['title']) : "";
		$time = isset( $_GET['uptime']) ? $_GET['uptime']  : "";
		$gt = isset( $_GET['gt'])  ? trim( $_GET['gt']) : "gt";

		$p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
		if( cookie('_P_NOVEL') && !isset( $_REQUEST['p'] ))
			$p = cookie("_P_NOVEL");
		if( $p< 1)
		$p = 1;
		$call = 0;
		$pall = 1;
		$wheres = array();
		$limits ='';
		$Ldata = NULL;
		$Mnovel  = D('Novel');
		$wheres['_string'] = '1=1';
		if( $ncid > 0)
			$wheres['ih_novel.ncid'] = array('eq', $ncid);
		if( $author)
			$wheres['author'] = array("like",'%'.$author.'%');
		if( $title)
			$wheres['title'] = array("like",'%'.$title.'%');
		if( $time )
			$wheres['utime'] = array( $gt , strtotime( $time));
		$call = $Mnovel->where( $wheres )->count();
		$pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
		if( $p > $pall )
		$p = $pall;
		$limits = ($p-1)*$this->a_psize;
		$limits.=','.$this->a_psize;
		$Ldata = $Mnovel->field("ih_novel.title,ih_novel.author,ih_novel.nid,ih_novel.utime,ih_novel.zimu,ih_novel.ncomm,ih_nclass.name as ncname")
		                ->join("ih_nclass on ih_novel.ncid=ih_nclass.ncid")
		                ->where( $wheres )
		                ->order('utime DESC')
		                ->limit( $limits )
		                ->select();
		
		$url = U('/Admin/Novel/index',array('p'=>'{!page!}'));
		cookie("_P_NOVEL" , $p);
		$pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);

		$this->assign("author", $author);
		$this->assign("title", $title);
		$this->assign("ncid" , $ncid);
		$this->assign("time" , $time );
		$this->assign("gt" , $gt);
		$this->assign("cates", $cates );
		$this->assign('call', $call );
		$this->assign('pnow', $p);
		$this->assign('nlist', $Ldata );
		$this->assign('pagestr', $pagestr );
		$this->display();
	}

	public function add()
  {
	 if( $this->isPost() )
	{
      $Mnovel = D("Novel");
	  $res = $Mnovel->create();
	  if( !$res ){
        $this->assign('jumpUrl', "javascript:history.go(-1);" );
		$this->error( $Mnovel->getError() );
	  }
	  else
	  {
        $Mnovel->add();
        $this->assign("jumpUrl",U('/Admin/Novel/index'));
		$this->success("小说添加成功");
	  }
	}
	 else
	{
	  $Mnclass = D('Nclass');
	  $class = array();
	  $class = $Mnclass->select();
	  $this->assign('nclass', $class );
	  $this->display();
	 }
	}

   public  function view(){
	if( $this->isGet() )
  {
    $id = isset( $_REQUEST['id']) ? intval( $_REQUEST['id']) : 0;
	$Mnovel = D("Novel");
	$wheres = array();
	$wheres['nid'] = array('eq', $id);
	$dNovel = $Mnovel->where( $wheres )->find();
	if( !$dNovel ){
        $this->assign("jumpUrl","javascript:history.go(-1);");
		$this->error("查看的小说不存在");
	}
	else
	{
      $Mnclass = D('Nclass');
	  $class = array();
	  $class = $Mnclass->select();
	  $this->assign('nclass', $class );
	  $tags = array();
	  $wheret['nid'] = array('eq',$dNovel['nid']);
	  $Mtindex = M("Tagindex");
	  $tags = $Mtindex->field("ih_tagindex.*,t.name as tagname")
		                  ->join("ih_tag t on t.tid = ih_tagindex.tid")
		                  ->where( $wheret )
		                  ->select();
	 $_tag = array();
	  foreach( $tags as $_v){
	   $_tag[] = $_v['tagname'];
	  }
     $this->assign('tags', implode('|',$_tag));
     $this->assign('d',$dNovel);
	 $this->display();
	}
   }
   else{
    $Mnovel = D("Novel");
    $res = $Mnovel->create();
	if( !$res ){
	 $this->assign("jumpUrl","javascript:history.go(-1);");
	 $this->error( $Mnovel->getError() );
	}
	else{
	 $Mnovel->save();
	 $this->assign("jumpUrl",U('/Admin/Novel/index'));
	 $this->success("修改信息保存成功");
	}
   }
  }

    //小说的章节
   public function chapters()
   {
    $nid = isset( $_REQUEST['nid']) ? intval( $_REQUEST['nid']) : 0;
	$Mnovel = D("Novel");
	$Mchapter = D("Nchapter");
	$dn = NULL;
	$wheres =array();
	$wheres['nid'] = array('eq',$nid);
	$dn = NULL;
	$dn = $Mnovel->where( $wheres )->find();
	if( !$dn ){
	 $this->assign("jumpUrl","javascript:history.go(-1);");
	 $this->error("查看的小说不存在");
	}
	else
	{
     $chaps = array();
	 $chaps= $Mchapter->where( $wheres )->order('ord ASC')->select();
	 $this->assign('ref',U('/Admin/Novel/chapters',array('nid'=>$nid)));
	 $this->assign('dlist' ,$chaps);
	 $this->assign("d" ,$dn);
	 $this->display();
	}
   }

   public function chapter_add()
   {
	  $Mchapter = D("Nchapter");
	if( $this->isPost() )
	{
       $ref = isset( $_POST['ref'] )  ? $_POST['ref'] : "javascript:history.go(-1);";
	   $res = $Mchapter->create();
	   if( !$res ){
	    $this->assign("jumpUrl",$ref);
		$this->error( $Mchapter->getError() );
	   }
	   else
	  {
         $Mchapter->add();
		 $this->assign("jumpUrl",$ref);
		 $this->success("小说章节添加成功");
	  }
	}
	else
	{
     $this->error("请求错误");
	}
   }

  public function chapter_edit()
 {
  $Mchapter = D("Nchapter");
  $ret = array("rcode"=>0,"msg"=>"服务器忙，请稍候再试","data"=>NULL);
  if( $this->isPost())
  {
	  $nid  = isset( $_POST['nid']) ? intval($_POST['nid']) : 0;
	  $res = $Mchapter->create();
	   $this->assign("jumpUrl",U('/Admin/Novel/chapters','nid='.$nid));
	  if( !$res ){
	   $this->error( $Mchapter->getError() );
	  }
	  else{
	   $Mchapter->save();
	   $this->success("章节数据更新成功");
	  }
  }
  else{
   $id = isset( $_GET['id']) ? intval( $_GET['id']) : 0;
   $nid = isset( $_GET['nid']) ? intval( $_GET['nid']) : 0;
   $wheres = array();
   $wheres['cpid'] = array('eq',$id);
   $dcp = NULL;
   $dcp =  $Mchapter->where( $wheres )->find();
   if( !$dcp )
   {
	  $ret['msg'] = "参数错误";
   }
   else{
    $this->assign("dcp",$dcp);
	$this->assign("nid",$nid);
	$Mnclass = D("Nclass");
	$class = $Mnclass->select();
	$this->assign("nclass",$class);
	 $ret['rcode'] = 1 ;
	 $ret['msg'] = "OK";
	 $ret['data'] = $this->fetch("Novel:div_chapter_edit");
   }
     echo  json_encode( $ret );
    exit();
  }
 }

  public function chapter_delete()
 {
 }

   public function contents()
  {
    $nid = isset( $_REQUEST['nid']) ? intval( $_REQUEST['nid']) : 0;
	$Mnovel = D("Novel");
	$Mcontent = D("Content");
	$dn = NULL;
	$wheres =array();
	$wheres['nid'] = array('eq',$nid);
	$dn = NULL;
	$dn = $Mnovel->where( $wheres )->find();

	if( !$dn ){
	 $this->assign("jumpUrl","javascript:history.go(-1);");
	 $this->error("查看的小说不存在");
	}
	else
	{
		$p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
		if( cookie('_P_NOVEL_CNT') && !isset( $_REQUEST['p'] ))
			$p = cookie("_P_NOVEL_CNT");
		if( $p< 1)
		  $p = 1;
		$call = 0;
		$pall = 1;
		$limits ='';
		$Ldata = NULL;
		$call = $Mcontent->where( $wheres )->count("*");
		$pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
		if( $p > $pall )
		  $p = $pall;
		$limits = ($p-1)*$this->a_psize;
		$limits.=','.$this->a_psize;
		$Ldata = $Mcontent->field("ncntid,cpid,nid,ncid,ord,title,ctime")->limit( $limits )->where( $wheres )->select();
		$url = U('/Admin/Novel/contents',array('p'=>'{!page!}','nid'=>$nid));
		$pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);
		cookie("_P_NOVEL_CNT" , $p );
		$this->assign("pagestr",$pagestr);
		$this->assign("call",$call);
		$this->assign("pnow",$p);
		$this->assign("list",$Ldata);
		$this->assign('ref',U('/Admin/Novel/contents',array('nid'=>$nid)));
		$this->assign("d" ,$dn);
		$this->display();
	}
  }

  public function content_add()
 {
   if( $this->isPost() )
  {
    $Mcontent = D("Content");
	$res = $Mcontent->create();
	if( !$res ){
	 $this->assign("jumpUrl","javascript:history.go(-1);");
	 $this->error( $Mcontent->getError() );
	}
	else{
	 $Mcontent->add();
	 $nid = isset( $_POST['nid']) ? intval( $_POST['nid']) :0 ;
	 $this->assign("jumpUrl",U('/Admin/Novel/contents',array('nid'=>$nid)));
     $this->success("小说内容添加成功");
	}
   }
   else
  {
	$nid = isset( $_GET['nid']) ? intval( $_GET['nid']) : 0;
	$wheres = array();
	$Mnovel = D("Novel");
	$du = NULL;
	$wheres['nid'] = array('eq',$nid);
	$du = $Mnovel->where( $wheres )->find();
	if( !$du )
	{
	   $this->assign("jumpUrl","javascript:history.go(-1);");
	   $this->error("添加的小说不存在!");
	}
	else{
	 $Mchapter = D("Nchapter");
	 $Mcid = D("Nclass");
	 $chpaters = $nclass = NULL;
	 $wherenc = array();
	 $chapters = $Mchapter->field("cpid,nid,ncid,title")->where( $wheres )->order("ord ASC")->select();
	 $nclass = $Mcid->field("ncid,name,state")->select();
	 $this->assign("chapters",$chapters);
	 $this->assign("nclass",$nclass);
     $this->assign('d',$du);
     $this->display();
	}
  }
 }

  public function content_edit()
  {
     if( $this->isGet() )
    {
     $nid = isset( $_GET['nid']) ? intval( $_GET['nid']) : 0;
	 $id =  isset( $_GET['id']) ? intval( $_GET['id']) : 0;
	 $p = isset( $_GET['p']) ? intval( $_GET['p']) : 1;
	 $Mcontent = D("Content");
	 $wheres = array();
	 $wherec = array();
	 $Mnovel = D("Novel");
	 $du = NULL;
	 $wheres['nid'] = array('eq',$nid);
	 $wherec['ncntid']= array('eq',$id);
	 $du = $Mnovel->where( $wheres )->find();
	 $dcnt = $Mcontent->where( $wherec )->find();
	 if( !$du  || !$dcnt)
	 {
	   $this->assign("jumpUrl","javascript:history.go(-1);");
	   $this->error("参数错误!");
	 }
	 else{
	  $this->assign("dcnt" , $dcnt);
	  $this->assign("p",$p);
	  $this->assign("d",$du);
	  $Mchapter = D("Nchapter");
	  $Mcid = D("Nclass");
	  $chpaters = $nclass = NULL;
      $wherenc = array();
	  $chapters = $Mchapter->field("cpid,nid,ncid,title")->where( $wheres )->order("ord ASC")->select();
	  $nclass = $Mcid->field("ncid,name,state")->select();
	  $this->assign("chapters",$chapters);
	  $this->assign("nclass",$nclass);
	  $this->display();
	 }
    }
    else
	{
      $nid = isset( $_POST['nid']) ? intval( $_POST['nid']) : 0;
	  $p = isset( $_POST['p']) ? intval( $_POST['p']) : 1;
	  $this->assign("jumpUrl",U('/Admin/Novel/contents',array('nid'=>$nid,'p'=>$p)));
	  $Mcontent =D("Content");
	  $res = $Mcontent->create();
	  if( !$res ){
	   $this->assign("jumpUrl","javascript:history.go(-1);");
	   $this->error("小说内容保存失败!");
	  }
	  else{
		$Mcontent->save();
	   $this->success("修改保存成功!");
	  }
	}
  }

  public function comments()
  {
	  $nid = isset( $_GET['nid']) ? intval( $_GET['nid']) : 0;
	  $d = array();
	  $d['nid'] = $nid;
	  $this->assign("d" , $d);
	  $this->display();
  }

  public function comment_delete(){
  }

}
?>