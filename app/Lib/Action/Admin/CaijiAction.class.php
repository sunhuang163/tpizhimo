<?php
/*--采集操作--*/
class CaijiAction extends BaseAction {
   private $caiji_size = 5; //每次解析5条记录防止，请求超时


	public function index()
	{
        //glob
        $orgPath = realpath( APP_PATH );
        $orgPath = str_replace("\\", "/", $orgPath);
        $orgPath .='/Lib/ORG/Caiji/';
        $ssresult = glob($orgPath.'*.class.php');
        $orgLib = array();
        foreach( $ssresult as $v)
        {
            $orgLib[] = basename( $v, '.class.php');
        }
        $orgInfo = array();

        $caijiPack = array();
        foreach( $orgLib as $vg )
        {
            import("@.ORG.Caiji.".$vg);
            $vc = new $vg();
            $vcinfo = $vc->m_class_info;
            $caijiPack[$vcinfo['key']] = $vcinfo;
            $Flist = F("_caiji/list".$vcinfo['key']);
            $Fnovel = F('_caiji/novel'.$vcinfo['key']);
            $vcinfo['list'] = $Flist ? count( $Flist ) : 0;
            $vcinfo['novel'] = $Fnovel ? count( $Fnovel) :0;
            $orgInfo[] = $vcinfo;
        }
        F('caijiModel',$caijiPack);
        $this->assign("orgs", $orgInfo);
		$this->display();
	}

	public function trash()
	{
		import("ORG.Io.Dir");
		$dpath = realpath( DATA_PATH );
		$dpath = str_replace( '\\', '/',  $dpath );
		$dpath .='/_caiji';
		$ff = glob($dpath.'/*.php');
		$dir = new Dir( $dpath );
        $dir->delDir( $dpath  );
        /*var_dump( $dpath  );
        $ss = $dir->getList( $dpath );
        var_dump( $ss );
		if(file_exists(DATA_PATH."_caiji/novel/") && !$dir->isEmpty(DATA_PATH."_caiji/novel/")){$dir->delDir(DATA_PATH."_caiji/novel/");}
		if(file_exists(DATA_PATH."_caiji/list/") && !$dir->isEmpty(DATA_PATH."_caiji/list/")){$dir->delDir(DATA_PATH."_caiji/list/");} */
		$this->assign("jumpUrl",U('/Admin/Caiji/index',array('t'=>time())));
		$this->success("采集缓存清空成功!");
	}

    //开始重新采集
    public function caiji()
    {
        if( $this->isGet() )
        {
        	$key = isset( $_GET['key']) ? trim( $_GET['key'] ) : '';
        	if( !$key )
        	{
        		$this->error("采集网站更新错误");
        	}
        	else
        	{
                $this->assign("caiji_key", $key );
                $list = F('_caiji/list'.$key);
                $p = isset( $_GET['p']) ? intval( $_GET['p']) : 1;
                $this->assign("call",count( $list ));
                if( isset( $list[$p]) )
                {
                    $url = U('/Admin/Caiji/caiji',array('p'=>'{!page!}','key'=>$key));
                    $pagestr = pagestr( $p ,count( $list) , urldecode($url) , 1);
                    $listContent = F('_caiji/list/'.$key.'/'.$p);
                    //加载已经采集的小说
                    $novellist = F('_caiji/novel'.$key);
                    if( $novellist && $listContent['d'] )
                    {
                        foreach( $listContent['d'] as &$v )
                        {
                            $key_index = md5($v['url']);
                            $v['detail_index'] = $key_index;
                            $v['is_detail'] = ( $novellist && isset( $novellist[$key_index])) ? 1 : 0;
                        }
                    }
                    $this->assign("novels", $listContent['d']);
                    $this->assign("keyp", $listContent['p']);
                    $this->assign("pagestr", $pagestr);
                }
                else
                    $this->assign("pagestr",'');
                $this->assign("list_key", $key);
                $this->assign("pnow", $p);
        		$this->display();
        	}
        }
        else
        {
            $res = array('rcode'=>0,'msg'=>'Server Busy','data'=>NULL);
            $op = isset( $_POST['op']) ? trim( $_POST['op']): FALSE;
            $key = isset( $_POST['key']) ? trim( $_POST['key']) : '';
            if( $op )
            {
            	$Mcaiji = FALSE;
                $arrcaijiModel = F('caijiModel');
                if( isset($arrcaijiModel[$key]) )
                {
                    import( $arrcaijiModel[$key]['package'] );
                    $class = $arrcaijiModel[$key]['class'];
                    $Mcaiji = new $class();
                }

                if( 'list' == $op)
                {
                    //解析列表页
                    $page = isset( $_POST['p']) ? intval( $_POST['p']) : 1;
                    if( $page == 0 )
                     	$p = 1;  //采集全部内容
                    else
                        $p = $page;
                    if( !$Mcaiji )
                    {
                        $res['msg'] = "采集模块不存在";
                    }
                    else
                    {
                        $caijiRes = $Mcaiji->getList( $p );
                        $res['pall'] = $caijiRes['page'];
                        if( $caijiRes['rcode'])
                        {
                            $res['msg'] = "采集成功";
                            $res['rcode'] = 1;
                            $res['data'] = $p;
                        }
                        else
                        {
                            $res['msg'] = $caijiRes['msg'];
                        }
                    }
                }
                else if( 'chapter' == $op )
                {
                	$url = isset( $_POST['url']) ? trim( $_POST['url']) : '';
                    if( !$Mcaiji )
                    {
                        $res['msg'] = "采集模块不存在";
                    }
                    else if(!$url )
                    {
                    	$res['msg'] = "参数提交错误";
                    }
                    else
                    {
                        $caijiRes = $Mcaiji->getChapter( $url );
                        if( $caijiRes['rcode'])
                       	{
                       		$res['rcode'] = 1;
                       		$res['msg'] = 'OK';
                       	}
                       	else
                       	{
                       		$res['msg'] = $caijiRes['msg'];
                       	}
                    }
                }
                else if('novel' == $op )
                {
                    //解析小说页面
                }
                else if('content' == $op )
                {
                    //解析小说的具体章节信息
                }
            }
            echo json_encode( $res );
            exit();
        }
    }


	//静态页面解析,源地址的，开始用ajax去解析
    public function listparse()
    {
        if( $this->isGet() )
        {
            $key = isset( $_GET['key']) ? trim( $_GET['key'] ) : '';
            $sindex = isset( $_GET['sindex']) ? trim( $_GET['sindex']) :'';
            if( !$key && $sindex )
            {
                $this->error("访问参数错误");
            }
            else
            {
                $list = F('_caiji/novel'.$key);
                $this->assign("call",count( $list ));
                if( isset( $list[$sindex]) )
                {
                    $listContent = F('_caiji/novel/'.$key.'/'.$sindex);
                    $this->assign("novels", $listContent['d']);
                    $this->assign("keyp", $listContent['p']);
                }
                else
                    $this->assign("pagestr",'');
                $this->assign("key", $key);
                $this->display();
            }
        }
        else
        {
            exit("parse OPtions ");
        }
    }

    //静态页面解析,列表页，开始用ajax去解析
    public function novelparse()
    {
        if( $this->isGet() )
        {
            $this->display();
        }
        else
        {
            exit("parse OPtions ");
        }
    }

    //静态页面解析,详情页面，开始用ajax去解析
    public function contentparse()
    {
        if( $this->isGet() )
        {
            $this->display();
        }
        else
        {
            exit("parse OPtions ");
        }
    }

}
?>