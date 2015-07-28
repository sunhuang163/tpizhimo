<?php
/***
 * @Date   2015/07/28
 * @Author ZhangYe
 * @@  day66.com 小说内容采集接口
 *
***/
if( !defined('ORG_CAIJI') )
	require_once('_Caiji.class.php');

class  Day66 extends _Caiji
{ 

	//初始化函数
	public function _initialize()
	{
		parent::_initialize();
		F("_caiji/list/".$this->m_cacheKey.'/t', time() );
		F("_caiji/novel/".$this->m_cacheKey.'/t', time() );
	}

	public function Day66( $_baseURL , $_orderType = 'time' , $_cacheType = 'File' , $_cacheKey = 'day66')
	{
		$this->m_baseURL   = $_baseURL;
		$this->m_orderType = $_orderType;
		$this->m_cacheType = $_cacheType;
		$this->m_cacheKey  = $_cacheKey;
		$this->_initialize();
	}

	public function t()
	{
		echo "test ORG ";
		$this->getNovel( 'http://www.day66.com/46068.aspx' );
	}

	//示例URL http://www.day66.com/Book/ShowBookList.aspx?tclassid=0&nclassid=0&page=1 
	public function getList( $p = 1)
	{
		$res = array('rcode'=>0,'msg'=>'Server Busy','data'=>NULL);
		$soso = $this->m_baseURL.'Book/ShowBookList.aspx?tclassid=0&nclassid=0&page={!p!}';
		$this->m_pageNow = $p;
		$dList = array();
	    $url = str_replace('{!p!}',$this->m_pageNow ,$soso);
	    $cnt = self::ss_cnt( $url );
	    if( $cnt )
	    {
	    	$reg='^class="bname"><a href="/(.*)" title="(.*)" target="_blank">^isU';
		    $reg_author = '^class="author">(.*)<^isU';
		    preg_match_all($reg, $cnt, $matches);
		    preg_match_all( $reg_author, $cnt, $match_authors);
		    if( $matches && isset($matches[2]) )
		    {
		     	foreach( $matches[1] as $_k=>$_v)
		     	{
		        	$item = array();
		    		$item['url'] = $this->m_baseURL.$_v;
		    		$item['title'] = trim($matches[2][$_k]);
		    		$item['author'] = trim($match_authors[1][$_k]); //获取作者，避免内容重复
		    		$dList[] = $item;
		     	}

		     	$cateData  = array('d' => $dList , 't'=>time(), 'p'=> $this->m_pageNow );
			    $cacheList = F('_caiji/list'.$this->m_cacheKey);
			    if( !$caheList )
			    {
			    	$cacheList = array();
			    }
			    $cacheList[$this->m_pageNow] = $this->m_pageNow;
			    F('_caiji/list'.$this->m_cacheKey , $cacheList );
			    F('_caiji/list/'.$this->m_cacheKey.'/'.$this->m_pageNow , $cateData );
			    $res['rcode'] = 0;
			    $res['msg']='OK';
			    $res['data'] = $dList;
		    }
		    else
		    {
		    	$res['msg'] = "网站模板修改，匹配失败";
		    }
			
	    }
	    else
	    	$res['msg'] = "抓取网页内容失败";

	    return $res;
	}

	//关于小说的额外信息，比如标题和作者等信息
	//示例 URL地址: http://www.day66.com/46068.aspx
    public function getNovel( $url  , $extData = NULL )
    {
    	$res = array('rcode'=>0,'msg'=>'Server Busy','data'=>NULL);
    	if( !$url )
    	{
    		$res['msg'] = "URL地址无效";
    	}
    	$novelCnt = self::ss_cnt( $url );
    	if( !$novelCnt )
    	{
    		$res['msg'] = "抓取网页内容失败";
    	}
    	else
    	{
    		$novelData = array();
    		$novelReg = array(
    				  	'title' => "#<h1>(.*)<\/h1>#is",
	  					'author' => "#class=\"author\">作者：(.*)<\/#isU",
					  	'pic' => '#<div class="fengmian">(.*)<\/div>#isU',
					  	'cate' => "#<span>类别：(.*)<\/span#iS",
				      	'tags' => "^<span>类别：(.*)<#iU^iUS",
				      	'utime' => "#<span>更新：(.*)<\/span>#isU",
					  	'ndesc' => "#<div class=\"bookintro\">(.*)<\/#isU",
					  	'nstate' =>'#(lianzai|wanjie)">(.*)<\/span>#isU',
    				);
    		foreach( $novelReg as $kp=>$vp )
    		{
    			 preg_match( $vp , $novelCnt , $match);
				if('pic' == $kp )
				{
					$strImg = $match[1];
					preg_match('#src="/(.*)"#isU' , $strImg , $match );
					$picurl ="";
					if( substr($match[1],-9) == "noimg.gif")
						$picurl = ''; //图片为空的话，就不显示图片
				  	else
				  		$picurl = $this->m_baseURL.$match[1] ;
				  	if( $picurl )
				  		$novelData['pic'] = $picurl;
				}
				else if( 'tags' == $kp )
				{
					//划分tag
				}
				else if('nstate' == $kp )
				{
					$novelData['uptxt'] = $match[2];
				}
				else
				{
					$novelData[$kp] = trim( $match[1] );
				}
    		}
    		var_dump( $novelData );
    		exit();
    		$nstate = "";
   			$novelData['uptxt']  = $nstate ? "已完结":"连载中";
   			$novelData['ctime'] = time();
   			$novelData['caijiurl'] = $url;

    		//更新内容到数据库
    	}

    	return $res;
    }

    public function getChapter()
    {
    	//
    }

    public function getContent()
    {
    	//
    } 

    private  function ss_cnt( $url  )
    {
    	$cnt = FALSE;
    	if( !preg_match('#^http#is' , $url ))
    	{
    		$url = $this->m_baseURL . $url ;
    	}
    	$cnt = curl_content( $url , $timeout );
	    if( $cnt)
	    {
	        $cnt =g2u( $cnt );
	    }	
	    return $cnt;
    }
}

?>