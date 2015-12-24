<?php
/**
* 后台采集内容
*
* @FILE 	CaijiAction.class.php
* @package 	Lib/Action
* @author 	ZhangYe
* @date     2015/12/01
*/
class CaijiAction extends BaseAction
{
   //每次解析5条记录防止，请求超时
   private $caiji_size = 5;


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
						$novelRes = FALSE;
						$novelRes = $Mcaiji->getNovel( $url );
                        if( $novelRes && $novelRes['rcode'] )
                        {
                            //判断内容是否添加
                            $Mnovel = D("Novel");
                            $wheres = array();
                            $wheres['title'] = array('eq', $novelRes['data']['title']);
                            $wheres['author'] = array('eq', $novelRes['data']['author']);
                            $novelFind = $Mnovel->field("nid,ncid")->where( $wheres )->find();
                            if( $novelFind )
                            {
                                $res['nid'] = $novelFind['nid'];
                                $res['ncid'] = $novelFind['ncid'];
                            }
                            else
                            {
                                $_POST['tags'] = $novelRes['data']['tags'];
                                if( $Mnovel->create( $novelRes['data'] ,3 ) )
                                {
                                    $res['nid'] = $Mnovel->add();
                                    $res['ncid'] = $novelRes['data']['ncid'];
                                }
                            }
                        }
                        $caijiRes = $Mcaiji->getChapter( $url );
                        if( $caijiRes['rcode'] )
                       	{
                       		$res['rcode'] = 1;
                       		$res['msg'] = 'OK';
                            //如果小说内容添加成功,则增加章节信息和具体的详情
                            if( $res['nid'] )
                            {
                                $Mchapter = D("Nchapter");
                                $Mcontent = D("Content");
                                $wheren = array();
                                $wheren['nid'] = array('eq', $res['nid']);
                                $wheren['ncid'] = array('eq', $res['ncid']);
                                //获取章节ID
                                $chapter_index = 1;
                                foreach( $caijiRes['data'] as $vo)
                                {
                                    $wheren['title'] = array('eq', $vo['cp']);
                                    $wheren['ord'] = array('eq', $chapter_index );
                                    $cp = $Mchapter->where( $wheren )->find();
                                    if( $cp )
                                    {
                                        $vo['cpid'] = $cp['cpid'];
                                    }
                                    else
                                    {
                                        $dcp = array();
                                        $dcp['nid'] = $res['nid'];
                                        $dcp['ncid'] = $res['ncid'];
                                        $dcp['title'] = $vo['cp'];
                                        $Mchapter->create( $dcp , 3 );
                                        $vo['cpid'] = $Mchapter->add();
                                    }
                                    $wherec = array();
                                    $wehrec['cpid'] = $vo['cpid'];
                                    $wehrec['nid'] = $res['nid'];

                                    $dcontent = array();
                                    $dcontent['nid'] = $res['nid'];
                                    $dcontent['ncid'] = $res['ncid'];
                                    $dcontent['cpid'] = $vo['cpid'];
                                    foreach( $vo['ext'] as $_vvc)
                                    {
                                        $wherec['title'] = array('eq', $_vvc['title']);
                                        $wherec['caijiurl'] = array('eq', $_vvc['url']);
                                        if( !$Mcontent->where( $wherec )->find() )
                                        {
                                            $dcontent['title'] = $_vvc['title'];
                                            $dcontent['caijiurl'] = $_vvc['url'];
                                            $dcontent['ctime'] = date();
                                            $dcontent['content'] = ' ';
                                            if( $Mcontent->create( $dcontent,3) )
                                                $Mcontent->add();
                                        }
                                    }
                                    $chapter_index++;
                                }
                                //
                            }
                            else
                            {
                                $res['msg'] = "获取小说编号失败";
                            }
                       	}
                       	else
                       	{
                       		$res['msg'] = $caijiRes['msg'];
                            echo "获取章节信息失败";
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
            //通过参数，判断小说是否添加
            $author = isset( $_GET['author']) ? trim( $_GET['author'] ) : FALSE;
            $title = isset( $_GET['title'] ) ? trim( $_GET['title']) : FALSE;
            $caijiurl = isset( $_GET['url']) ? trim( $_GET['url']) : FALSE;
            if( $author )
                $author = urldecode( $author );
            if( $title )
                $title = urldecode( $title );
            if( $caijiurl )
                $caijiurl = base64_decode( $caijiurl );
            $Mnovel = M("Novel");
            $wheren = array();

            if( (!$key || !$author || !$title || !$caijiurl ) && $sindex  )
            {
                $this->error("访问参数错误");
            }
            else
            {
                $novelData = FALSE;
                $wheren['title'] = array('eq', $title);
                $wheren['author'] = array('eq', $author );
                $wheren['caijiurl'] = array('eq', $caijiurl );
                $novelData =  $Mnovel->where( $wheren )->find();
                echo $Mnovel->getLastSql();

                $this->assign("novelData", $novelData );
                $this->assign("novelURL", $caijiurl);
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