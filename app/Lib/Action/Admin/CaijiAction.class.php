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
        foreach( $orgLib as $vg )
        {
            import("@.ORG.Caiji.".$vg);
            $vc = new $vg();
            $vcinfo = $vc->m_class_info;
            $Flist = F("_caiji/list".$vcinfo['key']);
            $Fnovel = F('_caiji/novel'.$vcinfo['key']);
            $vcinfo['list'] = $Flist ? count( $Flist ) : 0;
            $vcinfo['novel'] = $Fnovel ? count( $Fnovel) :0;
            $orgInfo[] = $vcinfo;
        }
        $this->assign("orgs", $orgInfo);
		$this->display();
	}

	public function t()
	{
		import("@.ORG.Caiji.Day66");
        echo "ORG test ";
		//$CDay66 = new Day66( 'http://www.day66.com/');
		//$CDay66->t();
	}

	public function trash()
	{
		import("ORG.Io.Dir");
		$dpath = realpath( DATA_PATH );
		$dpath = str_replace( '\\', '/',  $dpath );
		$dpath .='/';
		$dir = new Dir;
        //$dir->del( $dpath.'_caiji/' );
        $ss = $dir->getList( $dpath.'_caiji/' );
        var_dump( $ss );
		if(file_exists(DATA_PATH."_caiji/novel/") && !$dir->isEmpty(DATA_PATH."_caiji/novel/")){$dir->delDir(DATA_PATH."_caiji/novel/");}
		if(file_exists(DATA_PATH."_caiji/list/") && !$dir->isEmpty(DATA_PATH."_caiji/list/")){$dir->delDir(DATA_PATH."_caiji/list/");}
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
        		$this->display();
        	}
        }
        else
        {
            exit("caiji options");
        }
    }


	//静态页面解析,源地址的，开始用ajax去解析
    public function listparse()
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
