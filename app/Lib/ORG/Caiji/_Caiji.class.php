<?php
/***
 * @Date   2015/07/28
 * @Author ZhangYe
 * @@ 采集接口类的abstract基类
 *
***/
define('ORG_CAIJI' , TRUE );

abstract class _Caiji 
{ 
	protected $m_cacheType = "File";
	protected $m_cacheKey = "_Caiji";
	protected $m_baseURL = "";
	protected $m_novelCATE = array();
	protected $m_orderType = 'time'; //更新内容排序方式,time/hot
    protected $m_pageNow = 1;
    protected $m_res = array('rcode'=>0,'msg'=>'Server Busy','data'=>NULL);

	public function _initialize()
	{
		$this->m_novelCATE = C('WWW_CATE_ALL');
		//更新缓存目录
		F("_caiji/t", time());
		F("_caiji/list/t", time() );
		F("_caiji/novel/t",time() );
	}

	//根据分类名称返回分类
	public function cate( $catename )
	{
        foreach( $this->m_novelCATE as $v )
        {
            if( preg_match( "#".$v['tag']."#isU",$catename )){
    	       return $v['id'];
    	   }   
       }
       return 1;  //默认是未分类
	}

	abstract public function getList( $p = 1);  //获取列表页
    abstract public function getNovel( $url , $extData = array()); //获取小说详情页面
    abstract public function getChapter( $url ); //获取小说章节信息
    abstract public function getContent( $url );  //获取小说内容 
    abstract public function addNovel( $_data ); //添加添加小说内容
    abstract public function addContent( $_data ); //添加小说内容接口
}

?>