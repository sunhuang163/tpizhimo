<?php
/*--
   banfg56
   2014/11/30 星期日
   @@小说操作类
--*/
namespace app\admin\controller;

use app\common\controller\Base;
use \think\Input;

class Novel extends Base
{
	public function index()
	{
		//设置列表URL
		cookie("LURL" , $_SERVER['REQUEST_URI']);
		$Mnclass = new \app\common\model\Nclass;
		$cates  = [];
		$cates = $Mnclass::field("ncid,name")->select();
		/*search condition*/
		$ncid = $_GET['ncid'] ?? -1;
		$author = $_GET['author'] ?? "";
		$title = $_GET['title'] ?? "";
		$time = $_GET['uptime'] ?? "";
		$gt = $_GET['gt'] ?? "gt";

		$p = Input::request("p",1,'intval');
		if( cookie('_P_NOVEL') && !isset( $_REQUEST['p'] ))
			$p = cookie("_P_NOVEL");
		if( $p< 1)
		$p = 1;
		$call = 0;
		$pall = 1;
		$wheres = array();
		$limits ='';
		$Ldata = NULL;
		$Mnovel  = new \app\common\model\Novel;
		if( $ncid > 0)
			$wheres['n.ncid'] = array('eq', $ncid);
		if( $author)
			$wheres['author'] = array("like",'%'.$author.'%');
		if( $title)
			$wheres['title'] = array("like",'%'.$title.'%');
		if( $time )
			$wheres['utime'] = array( $gt , strtotime( $time));
		$call = $Mnovel::where( $wheres )->count();
		$pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
		if( $p > $pall )
		$p = $pall;
		$limits = ($p-1)*$this->a_psize;
		$limits.=','.$this->a_psize;
		$Ldata = $Mnovel::alias('n')
						->field("n.title,n.author,n.nid,n.pic,n.ctime,n.utime,n.zimu,n.ncomm,nc.name as ncname")
		                ->join("nclass nc","n.ncid=nc.ncid")
		                ->where( $wheres )
		                ->order('utime DESC')
		                ->limit( $limits )
		                ->select();

		$url = url('/admin/Novel/index',array('p'=>'{!page!}'));
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
		return $this->fetch();
	}

	public function add()
	{
		if( IS_POST )
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
	    		$listURL = cookie("LURL") ? cookie("LURL") : url('/Admin/Novel/index');
	    		$this->assign("jumpUrl",$listURL );
				return $this->success("小说添加成功");
	  		}
		}
	 	else
		{
	  		$Mnclass = new \app\common\model\Nclass;
	 		$class = array();
	  		$class = $Mnclass::All();
	  		$this->assign('nclass', $class );
	  		return $this->fetch();
	 	}
	}

	public  function view()
	{
		if( IS_GET )
		{
			$id = $_REQUEST['id'] ?? 0;
			$Mnovel = D("Novel");
			$wheres = [];
			$wheres['nid'] = array('eq', $id);
			$dNovel = $Mnovel->where( $wheres )->find();
			if( !$dNovel ){
		    	$this->assign("jumpUrl","javascript:history.go(-1);");
				$this->error("查看的小说不存在");
			}
			else
			{
		  		$Mnclass = new \app\common\model\Nclass;
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
		else
		{
			$Mnovel = new \app\common\model\Novel;
			$res = $Mnovel->create();
			if( !$res )
			{
		 		$this->assign("jumpUrl","javascript:history.go(-1);");
		 		$this->error( $Mnovel->getError() );
			}
			else
			{
				$listURL = cookie("LURL") ? cookie("LURL") : url('/Admin/Novel/index');
		 		$Mnovel->save();
		 		$this->assign("jumpUrl", $listURL );
		 		$this->success("修改信息保存成功");
			}
		}
	}

    //小说的章节
	public function chapters()
	{
		$nid = isset( $_REQUEST['nid']) ? intval( $_REQUEST['nid']) : 0;
		cookie("LURL" , $_SERVER['REQUEST_URI']);
		$Mnovel = D("Novel");
		$Mchapter = D("Nchapter");
		$dn = NULL;
		$wheres =array();
		$wheres['nid'] = array('eq',$nid);
		$dn = NULL;
		$dn = $Mnovel->where( $wheres )->find();
		if( !$dn )
		{
	 		$this->assign("jumpUrl","javascript:history.go(-1);");
	 		$this->error("查看的小说不存在");
		}
		else
		{
	 		$chaps = array();
	 		$chaps= $Mchapter->where( $wheres )->order('ord ASC')->select();
	 		$this->assign('ref',url('/Admin/Novel/chapters',array('nid'=>$nid)));
	 		$this->assign('dlist' ,$chaps);
	 		$this->assign("d" ,$dn);
	 		$this->display();
		}
	}

	public function chapter_add()
	{
		$Mchapter = D("Nchapter");
		if( IS_POST )
		{
	   		$listURL = cookie("LURL") ? cookie("LURL") : url('/Admin/Novel/view',array('id'=>$Mchapter->nid ));
	   		$res = $Mchapter->create();
	   		if( !$res )
	   		{
	    		$this->assign("jumpUrl",$listURL);
				$this->error( $Mchapter->getError() );
	   		}
	   		else
	  		{
	     		$Mchapter->add();
		 		$this->assign("jumpUrl",$listURL);
		 		$this->success("小说章节添加成功");
	  		}
		}
		else
			$this->error("请求错误");
	}

	public function chapter_edit()
	{
		$Mchapter = D("Nchapter");
		$ret = array("rcode"=>0,"msg"=>"服务器忙，请稍候再试","data"=>NULL);
		if( IS_POST)
		{
	  		$nid  = isset( $_POST['nid']) ? intval($_POST['nid']) : 0;
	  		$res = $Mchapter->create();
	  		$listURL = cookie("LURL") ? cookie("LURL") : url('/Admin/Novel/view',array('id'=>$nid ));
	   		$this->assign("jumpUrl", $listURL );
	  		if( !$res )
	  		{
	   			$this->error( $Mchapter->getError() );
	 	 	}
	  		else
	  		{
	   			$Mchapter->save();
	   			$this->success("章节数据更新成功");
	  		}
		}
		else
		{
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
			else
			{
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
		//
	}

	//章节标题移动
	public function chapter_move()
	{
		//
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

		if( !$dn )
		{
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
			$url = url('/Admin/Novel/contents',array('p'=>'{!page!}','nid'=>$nid));
			$pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);
			cookie("_P_NOVEL_CNT" , $p );
			$this->assign("pagestr",$pagestr);
			$this->assign("call",$call);
			$this->assign("pnow",$p);
			$this->assign("list",$Ldata);
			$this->assign('ref',url('/Admin/Novel/contents',array('nid'=>$nid)));
			$this->assign("d" ,$dn);
			$this->display();
		}
	}

	public function content_add()
	{
		if( IS_POST )
		{
			$Mcontent = D("Content");
			$res = $Mcontent->create();
			if( !$res )
			{
		 		$this->assign("jumpUrl","javascript:history.go(-1);");
		 		$this->error( $Mcontent->getError() );
			}
			else
			{
		 		$Mcontent->add();
		 		$nid = isset( $_POST['nid']) ? intval( $_POST['nid']) :0 ;
		 		$this->assign("jumpUrl",url('/Admin/Novel/contents',array('nid'=>$nid)));
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
			else
			{
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
		 if( IS_GET )
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
		 	else
		 	{
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
		 	$this->assign("jumpUrl",url('/Admin/Novel/contents',array('nid'=>$nid,'p'=>$p)));
		  	$Mcontent =D("Content");
		  	$res = $Mcontent->create();
		  	if( !$res )
		  	{
		   		$this->assign("jumpUrl","javascript:history.go(-1);");
		   		$this->error("小说内容保存失败!");
		  	}
		  	else
		  	{
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