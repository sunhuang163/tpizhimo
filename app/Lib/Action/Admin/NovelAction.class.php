<?php
/*--
   banfg56
   2014/11/30 星期日
   @@小说操作类
--*/
class NovelAction extends BackAction {
	public function index()
	{
      $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
	  if( $p< 1)
		  $p = 1;
	  $call = 0;
	  $pall = 1;
	  $wheres = array();
	  $limits ='';
	  $Ldata = NULL;
	  $Mnovel  = D('Novel');
	  $wheres['_string'] = '1=1';
      $call = $Mnovel->where( $wheres )->count("*");
      $pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
	  if( $p > $pall )
		  $p = $pall;
	  $limits = ($p-1)*$this->a_psize;
	  $limits.=','.$this->a_psize;
	  $Ldata = $Mnovel->field("ih_novel.*,ih_nclass.name as ncname")
		  ->join("ih_nclass on ih_novel.ncid=ih_nclass.ncid")
		  ->where( $wheres )->order('utime DESC')
		  ->limit( $limits )->select();
	  $url = U('/Admin/Novel/index',array('p'=>'{!page!}'));
      $pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);

	  $this->assign('call' ,$call );
	  $this->assign('pnow' , $p);
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
    $id = isset( $_REQUEST['id']) ? intval( $_POST['id']) : 0;
	$Mnovel = D("Novel");
	$wheres = array();
	$wehres['nid'] = array('eq', $id);
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

 }

  public function chapter_delete()
 {
 }

   public function contents()
  {
	 if( $this->isPost() ){
	 }
	 else
	{
     $nid = isset( $_REQUEST['nid']) ? intval( $_REQUEST['nid']) : 0;
	$Mnovel = D("Novel");
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
	 $this->assign('ref',U('/Admin/Novel/contents',array('nid'=>$nid)));
	 $this->assign("d" ,$dn);
	 $this->display();
	}
	 }
  }

  public function content_add()
 {
   if( $this->isPost() ){

   }
   else
  {
    $this->display();
  }
 }

  public function content_edit()
  {
  }

  public function comments(){
	  $this->display();
  }

  public function comment_delete(){
  }

}
?>