<?php
/*--
  banfg56
  2014/11/30 星期日
  @@小说分类管理
--*/
namespace app\admin\controller;

use app\common\controller\Base;
use \think\Input;

class Nclass extends Base
{
    //main page
	public function index()
	{
		$p = Input::request('p',1,'intval');
		if( $p< 1)
			$p = 1;
		$call = 0;
		$pall = 1;
		$limits ='';
		$Ldata = NULL;
		$Dclass  = new \app\common\model\Nclass;
		$call = $Dclass::count();
		$pall = ($call >0) ? ceil($call/$this->a_psize) : 1;
		if( $p > $pall )
		  $p = $pall;
		$limits = ($p-1)*$this->a_psize;
		$limits.=','.$this->a_psize;
		$Ldata = $Dclass::limit( $limits )->order("ord","ASC")->select();
		$url = url('/admin/Nclass/index',array('p'=>'{!page!}'));
		$pagestr = pagestr( $p , $pall , urldecode($url) , $this->a_psize);
		$this->assign('call' ,$call );
		$this->assign('pnow' , $p);
		$this->assign('clist', $Ldata );
		$this->assign('pagestr', $pagestr );
		return $this->fetch();
	}

	public function add()
	{
		if( IS_POST)
		{
			$Mclass  = new \app\common\model\Nclass( $_POST );
			$res = $Mclass->create();
		  	if( $Mclass->save() )
		  	{
		   		$this->assign('jumpUrl',url('/Admin/Nclass/index'));
		   		return $this->success("分类添加成功");
		 	}
		  	else
		  	{
		   		$this->assign("jumpUrl","javascript:history.go(-1);");
		   		return $this->error("添加分配失败");
		  	}
		}
	}

	//目录的显示和隐藏
	  public function ban()
	{
		$Mnclass = new \app\common\model\Nclass;
		$ncid = Input::post('ncid',0,'intval');
		$state = Input::post('state',0,'intval');
		$res =['rcode'=>0,'msg'=>'服务器忙，请稍后再试','data'=>NULL];
		$wheres = [];
		$wheres['ncid'] = ['eq', $ncid];
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
		$res = ['rcode'=>0,'msg'=>'服务器忙，请稍后再试','data'=>NULL];
		$id =Input::post('id',0,'intval');
		$type = Input::post('type','','trim');

		if( !$id || !$type)
		{
			$res['msg'] = "提交参数错误";
		}
		else
		{
			$Mnclass = new \app\common\model\Nclass;
			$dn = NULL;
			$wheres= [];
			$dn = \think\Db::name("nclass")->field("ord")->where( 'ncid',$id )->find();
			if( $dn )
			{
		 		if( $type == 'up')
		 		{
		  			$wheres['ord'] = array('lt',$dn['ord']);
		  			$dnext = NULL;
		  			$dnext = \think\Db::name("nclass")->field('ncid,ord')->where( $wheres )->order("ord DESC")->find();
		  			if( !$dnext )
		  			{
		    			$res['msg'] = "该分类已经是第一位";
		  			}
		  			else
		  			{
					    $d = [];
						$whereu = [];
						$d['ord'] = $dn['ord'];
					    $whereu['ncid']= array('eq',$dnext['ncid']);
						\think\Db::name("nclass")->where( $whereu )->update( $d );
						$d['ord'] = $dnext['ord'];
						$whereu['ncid'] = array('eq',$id);
						\think\Db::name("nclass")->where( $whereu )->update( $d );
						$res['msg']  = "OK";
						$res['rcode'] = 1;
						$Mnclass->_cache();
		  			}
		 		}
				else if( $type == 'down')
				{
					$wheres['ord'] = array('gt',$dn['ord']);
					$dnext = NULL;
					$dnext = \think\Db::name("nclass")->field('ncid,ord')->where( $wheres )->order("ord ASC")->find();
					if( $dnext )
					{
						$whereu = $d = [];
						$d['ord'] = $dn['ord'];
						$whereu['ncid'] = array('eq',$dnext['ncid']);
						\think\Db::name("nclass")->where( $whereu )->update( $d );
						$d['ord'] = $dnext['ord'];
						$whereu['ncid'] = array('eq',$id);
						\think\Db::name("nclass")->where( $whereu )->update( $d );
						$res['msg'] = "OK";
						$res['rcode'] = 1;
						$Mnclass->_cache();
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
		$Mnclass = NULL;
		$wheres = [];
		if( IS_POST )
		{
		  	$Mnclass =  new \app\common\model\Nclass( $_POST );
		  	if( $Mnclass->update() )
		  	{

		   		$this->assign('jumpUrl',url('/Admin/Nclass/index'));
		   		return $this->success("资料保存成功");
		  	}
		  	else
		  	{
		   		$this->assign("jumpUrl","javascript:history.go(-1);");
		   		return $this->error("小说分类信息保存失败");
		  	}
		}
		else
		{
			$id = Input::get('id',0,'intval');
			$du = NULL;
			$Mnclass =  new \app\common\model\Nclass;
			$du = $Mnclass::get(["ncid"=>$id ]);
			if( $du )
			{
		  		$this->assign('d',$du);
		  		return $this->fetch();
			}
			else
			{
				$this->assign("jumpUrl" ,url('/Admin/Nclass/index'));
				return $this->error("编辑的小说分类不存在");
			}
		}
	}
}
?>