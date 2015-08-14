<?php
/*--
  banfg56
  2014/11/30 星期日
  @@小说分类管理
--*/
class NclassAction extends BaseAction {
    //main page
	public function index()
	{
		$p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
		if( $p< 1) $p = 1;
		$call = 0;
		$pall = 1;
		$limits ='';
		$Ldata = NULL;
		$Dclass  = D('Nclass');
		$call = $Dclass->count("*");
		$pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
		if( $p > $pall )
		  $p = $pall;
		$limits = ($p-1)*$this->a_psize;
		$limits.=','.$this->a_psize;
		$Ldata = $Dclass->limit( $limits )->order("ord ASC")->select();
		$url = U('/Admin/Nclass/index',array('p'=>'{!page!}'));
		$pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);


		$this->assign('call' ,$call );
		$this->assign('pnow' , $p);
		$this->assign('clist', $Ldata );
		$this->assign('pagestr', $pagestr );
		$this->display();
	}

	public function add()
	{
		if( $this->isPost() )
		{
			$Mclass = D('Nclass');
			$res = $Mclass->create();
		  	if( $res )
		  	{
		   		$Mclass->add();
		   		$this->assign('jumpUrl',U('/Admin/Nclass/index'));
		   		$this->success("分类添加成功");
		 	}
		  	else
		  	{
		   		$this->assign("jumpUrl","javascript:history.go(-1);");
		   		$this->error( $Mclass->getError() );
		  	}
		}
	}

	//目录的显示和隐藏
	  public function ban()
	{
		$Mnclass = D("Nclass");
		$ncid = isset( $_POST['ncid']) ? intval($_POST['ncid']):0;
		$state = isset( $_POST['state'] ) ? intval( $_POST['state']) :0;
		$res = array('rcode'=>0,'msg'=>'服务器忙，请稍后再试','data'=>NULL);
		$wheres = array();
		$wheres['ncid'] = array('eq', $ncid);
		$d = array();
		$d['state'] = 1==$state ? 0 : 1 ;
		$res['rcode'] = $Mnclass->data( $d )->where( $wheres )->save();
		if( $res['rcode'] )
		{
		  	$res['msg']='OK';
		   	$res['rcode'] = 1;
		}
		else
			$res['msg'] = '状态更新失败';

		echo json_encode( $res );
		exit();
	}

	/**
	*目录排序的修改
	*/
	public function move()
	{
		$res = array('rcode'=>0,'msg'=>'服务器忙，请稍后再试','data'=>NULL);
		$id = isset( $_POST['id']) ? intval( $_POST['id']) : 0;
		$type = isset( $_POST['type']) ? trim( $_POST['type']) : "";

		if( !$id || !$type)
		{
			$res['msg'] = "提交参数错误";
		}
		else
		{
			$Mnclass = M("Nclass");
			$Mdocache = D("Nclass");
			$dn = NULL;
			$wheres = array();
			$wheres['ncid'] = array('eq',$id);
			$dn = $Mnclass->field("ord")->where( $wheres )->find();
			unset( $wheres['ncid']);
			if( $dn )
			{
		 		if( $type == 'up')
		 		{
		  			$wheres['ord'] = array('lt',$dn['ord']);
		  			$dnext = NULL;
		  			$dnext = $Mnclass->field('ncid,ord')->where( $wheres )->order("ord DESC")->find();
		  			$res['ff'] = mysql_error();
		  			$res['f'] = $Mnclass->getLastSql();
		  			if( !$dnext )
		  			{
		    			$res['msg'] = "该分类已经是第一位";
		  			}
		  			else
		  			{
					    $d = array();
						$whereu = array();
						$d['ord'] = $dn['ord'];
					    $whereu['ncid']= array('eq',$dnext['ncid']);
						$Mnclass->where( $whereu )->save( $d );
						$d['ord'] = $dnext['ord'];
						$whereu['ncid'] = array('eq',$id);
						$Mnclass->where( $whereu )->save( $d );
						$res['msg']  = "OK";
						$res['rcode'] = 1;
						$Mdocache->_cache();
		  			}
		 		}
				else if( $type == 'down')
				{
					$wheres['ord'] = array('gt',$dn['ord']);
					$dnext = NULL;
					$dnext = $Mnclass->field('ncid,ord')->where( $wheres )->order("ord ASC")->find();
					if( $dnext )
					{
						$d = array();
						$whereu = array();
						$d['ord'] = $dn['ord'];
						$whereu['ncid'] = array('eq',$dnext['ncid']);
						$Mnclass->where( $whereu )->save( $d );
						$res['ff'] = $Mnclass->getLastSql();
						$d['ord'] = $dnext['ord'];
						$whereu['ncid'] = array('eq',$id);
						$Mnclass->where( $whereu )->save( $d );
						$res['msg'] = "OK";
						$res['rcode'] = 1;
						$Mdocache->_cache();
					}
					else
					{
						$res['msg'] = "该分类已经是最后一位";
					}
				}
		 		else
		 		{
		  			$res['msg'] = "参数提交错误";
		 		}
			}
			else
			{
				$res['msg'] = "操作对象不存在";
			}
		}
		echo json_encode( $res );
		exit();
	}

	//目录资料修改
	public function edit()
	{
		$Mnclass = D("Nclass");
		$wheres = array();
		if( $this->isPost() )
		{
		  	$res = $Mnclass->Create();
		  	if( $res )
		  	{
		   		$Mnclass->save();
		   		$this->assign('jumpUrl',U('/Admin/Nclass/index'));
		   		$this->success("资料保存成功");
		  	}
		  	else
		  	{
		   		$this->assign("jumpUrl","javascript:history.go(-1);");
		   		$this->error("小说分类信息保存失败");
		  	}
		}
		else
		{
			$id = isset( $_GET['id']) ? intval( $_GET['id']) : 0;
			$wheres['ncid'] = array('eq',$id);
			$du = NULL;
			$du = $Mnclass->where( $wheres )->find();
			if( $du )
			{
		  		$this->assign('d',$du);
		  		$this->display();
			}
			else
			{
				$this->assign("jumpUrl" ,U('/Admin/Nclass/index'));
				$this->error("编辑的小说分类不存在");
			}
		}
	}
}
?>