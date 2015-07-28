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

	public function _initialize()
	{
		$this->m_novelCATE = C('WWW_CATE_ALL');
		//更新缓存目录
		F("_caiji/t", time());
		F("_caiji/list/t", time() );
		F("_caiji/novel/t",time() );
	}
	abstract public function getList( $p = 1);  //获取列表页
    abstract public function getNovel( $url , $extData = array()); //获取小说详情页面
    abstract public function getChapter(); //获取小说章节信息
    abstract public function getContent();  //获取小说内容  
}

?>