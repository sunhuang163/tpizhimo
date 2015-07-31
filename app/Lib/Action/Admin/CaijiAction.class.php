<?php
/*--采集操作--*/
class CaijiAction extends BaseAction {
   private $caiji_size = 5; //每次解析5条记录防止，请求超时


	public function index()
	{
		 $novels  = F("_caiji/novel");
		 $lists = F("_caiji/list");
		 $this->assign("npage", $lists ? count($lists) : 0);
		 $this->assign("nnovel", $novels ? count($novels) : 0);
		 $this->display();
	}

	public function t()
	{
		import("@.ORG.Caiji.Day66");
		$CDay66 = new Day66( 'http://www.day66.com/');
		$CDay66->t();
	}

	public function trash()
	{
		import("ORG.Io.Dir");
		$dir = new Dir;
		@unlink(DATA_PATH.'_caiji/list');
		@unlink(DATA_PATH.'_caiji/novel');
		if(file_exists(DATA_PATH."_caiji/novel/") && !$dir->isEmpty(DATA_PATH."_caiji/novel/")){$dir->del(DATA_PATH."_caiji/novel/");}
		if(file_exists(DATA_PATH."_caiji/list/") && !$dir->isEmpty(DATA_PATH."_caiji/list/")){$dir->del(DATA_PATH."_caiji/list/");}
		$this->assign("jumpUrl",U('/Admin/Caiji/index',array('t'=>time())));
		$this->success("采集缓存清空成功!");
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
