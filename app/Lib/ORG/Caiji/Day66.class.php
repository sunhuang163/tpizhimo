<?php
/***
 * @Date   2015/07/28
 * @Author ZhangYe
 * @@  day66.com 小说内容采集接口
 *
***/
if( !defined('ORG_CAIJI') )
	require_once('_Caiji.php');

class  Day66 extends _Caiji
{
    public $m_class_info = array(
                        'name'=>'天天小说网',
                        'source'=>'http://www.day66.com/',
                        'key'=>'day66',
                        'package'=>'@.ORG.Caiji.Day66',
                        'class'=>'Day66'
                        );
	//初始化函数
	public function _initialize()
	{
		parent::_initialize();
		F("_caiji/list/".$this->m_cacheKey.'/t', time() );
		F("_caiji/novel/".$this->m_cacheKey.'/t', time() );
	}

	public function Day66( $_baseURL , $_orderType = 'time' , $_cacheType = 'File' , $_cacheKey = 'day66')
	{
		$this->m_baseURL   = $_baseURL ? $_baseURL : 'http://www.day66.com/';
		$this->m_orderType = $_orderType;
		$this->m_cacheType = $_cacheType;
		$this->m_cacheKey  = $_cacheKey;
		$this->_initialize();
	}

	//示例URL http://www.day66.com/Book/ShowBookList.aspx?tclassid=0&nclassid=0&page=1
	public function getList( $p = 1)
	{
		$res = $this->m_res;
		$soso = $this->m_baseURL.'Book/ShowBookList.aspx?page={!p!}';
		$this->m_pageNow = $p;
		$dList = array();
	    $url = str_replace('{!p!}',$this->m_pageNow ,$soso);
	    $cnt = self::ss_cnt( $url );
	    if( $cnt )
	    {
            /**
             * 列表作者匹配失败,第一个作者是列表头BUG,2015/09/10
             *
             * @author ZhangYe
             */
            $cnt = preg_replace('^class="liinfo">(.*)<\/li>^isU', '', $cnt);
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
			    if( !$cacheList )
			    {
			    	$cacheList = array();
			    }
                $reg_all_page = '^page=([\d]+)">末页<\/a>^isU';
                if( preg_match($reg_all_page, $cnt, $mpage))
                {
                    $res['page'] = intval( $mpage[1] );
                }
                else
                    $res['page'] = 0;
			    $cacheList[$this->m_pageNow] = $this->m_pageNow;
			    F('_caiji/list'.$this->m_cacheKey , $cacheList );
			    F('_caiji/list/'.$this->m_cacheKey.'/'.$this->m_pageNow , $cateData );
			    $res['rcode'] = 1;
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
    public function getNovel( $url )
    {
    	$res = $this->m_res;
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
					  	'cate' => "#<span>类别：(.*)<\/span#isU",
				      	'tags' => "#<span>类别：(.*)<#iUs",
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
				else if('nstate' == $kp )
				{
					$novelData['uptxt'] = $match[2];
				}
				else
				{
					$novelData[$kp] = trim( $match[1] );
				}
    		}
    		$novelData['nstate'] = ('连载中' == $novelData['uptxt']) ? 0 : 1;//小说是否已经完结
   			$novelData['caijiurl'] = $url;
   			$res['rcode'] = 1;
   			$res['msg'] = 'OK';
   			$res['data'] = $novelData;
    	}

    	return $res;
    }

    //获取小说的章节信息
    //示例 URL http://www.day66.com/xiaoshuo/48/48953/Index.shtml
    public function getChapter( $url )
    {
    	$res = $this->m_res;
    	if( !$url )
    	{
    		$res['msg'] = "URL地址无效";
    	}
        else
        {
        	$IndexCnt = self::ss_cnt( $url );
        	if( !$IndexCnt )
        	{
        		$res['msg'] = "抓取网页内容失败";
        	}
        	else
        	{
        		$RegChanpter = "#<dt>(.*)<\/a>(.*)<\/dt>#isU";
        		$RegContent = "#<\/dt>(.*)(<dt>|<\/dl>)#isU";
        		$RegDetail = "#<dd><a href=\"(.*)\" title=\"(.*)\">(.*)<\/a><\/dd>#isU";
        		$chaps = array();
                $novelURL = substr( $url, 0, strrpos($url , "/")+1 );

        		if( preg_match_all($RegChanpter, $IndexCnt, $mathCp) && preg_match_all($RegContent, $IndexCnt, $matchCDiv ) )
        		{
        			$chaps = $mathCp[2];
                    $novelData = array();
                    $ic = 1;
        			foreach( $matchCDiv[1] as $kc=>$vc )
        			{
                        $items = array('k'=>0,'cp'=>'','ext'=>NULL);
        				preg_match_all( $RegDetail , $vc , $matchRefs );
                        $items['k'] = $ic;
                        $items['cp'] = $chaps[$kc];
        				$item = array();
                        foreach( $matchRefs[1] as $k_ref=>$v_ref )
                        {
                            $ss = array('url'=>'','title'=>'');
                            $ss['url'] = $novelURL.$v_ref;
                            $ss['title'] = trim( $matchRefs[2][$k_ref] );
                            $item[] = $ss;
                        }
                        $items['ext'] = $item;
                        $novelData[] = $items;
                        $ic++;
        			}
                    $res['data'] = $novelData;
                    $res['rcode'] = 1;
                    $res['msg'] = 'OK';
                    //更新内容到缓存目录
                    $cateData  = array('d' => $novelData , 't'=>time(), 'p'=> '' );
                    $cateData['p'] = md5( $url );
                    $cacheList = F('_caiji/novel'.$this->m_cacheKey);
                    if( !$caheList )
                    {
                        $cacheList = array();
                    }
                    $cacheList[$cateData['p']] = $cateData['p'];

                    F('_caiji/novel'.$this->m_cacheKey , $cacheList );
                    F('_caiji/novel/'.$this->m_cacheKey.'/'.$cateData['p'], $cateData );
        		}
        	}
        }

    	return $res;
    }

    //获取小说的详情
    //示例URL http://www.day66.com/xiaoshuo/48/48953/9756993.shtml , 山贼卷
    public function getContent( $url )
    {
    	$res = $this->m_res;
        if( !$url )
        {
            $res['msg'] = "参数错误";
        }
        else
        {
            $novelCnt = self::ss_cnt( $url );
            if( !$novelCnt )
            {
                $res['msg'] = "抓取网页内容失败";
            }
            else
            {
                $Cntinfo = array('title'=>'','url'=>'','cnt'=>'');
                $Cntinfo['url'] = $url;
                $RegCnt = array(
                                'title'=>"#<h2>(.*)<\/h2>#isU",
                                'cnt' => "#id=\"htmlContent\" class=\"yd_text2\">(.*)</div>\s+<div class=\"yd_ad3\">#isU"
                                );
                foreach( $RegCnt as $kr=>$vr )
                {
                    preg_match( $vr, $novelCnt, $match );
                    $Cntinfo[$kr] = trim( $match[1] );
                }
                $Cntinfo['cnt'] = $this->htmlclean( $Cntinfo['cnt'] );
                $res['rcode'] = 1;
                $res['msg'] = "OK";
                $res['data'] = $Cntinfo;
            }
        }
        return $res;
    }

    public function addNovel( $_data )
    {
    	$ret = $this->m_res;
    	if( $_data )
    	{
    		$MNovel = D("Novel");
    		$wheres = array();

    		$wheres['title'] = array('eq', $_data['title'] );
       		$wheres['author'] = array('eq', $_data['author']);
       		$find = $MNovel->field("nid,pic")->where( $wheres)->find();
       		//该小说已经存在
       		if( $find )
       		{
       			$ret['msg'] = "该小说已经存在";
       			$ret['data'] = $find['nid']; //结果中返回该小说的编号
       		}
       		else
       		{
       			$addRes = $MNovel->create( $_data , 3);
       			if( !$addRes )
       			{
       				$ret['msg'] = "创建表单失败，错误信息：".$MNovel->getError();
       			}
       			else
       			{
       				//获取分类信息
       				$_data['ncid'] = $this->cate( $_data['cate'] );
       				//更新tags
       				$_POST['tags'] = $_data['tags'];
       				$novel_id = $MNovel->add();
       				$ret['rcode'] = 1;
       				$ret['msg'] = 'OK';
       				$ret['data'] = $novel_id;
       			}
       		}

    	}
    	else
    	{
    		$ret['msg'] = "参数不能为空";
    	}

    	return $ret ;
    }

    public function addContent( $_data )
    {
    	$res = $this->m_res;
        if( !$_data )
        {
            $res['msg'] = "参数不能为空";
        }
        else
        {
            $Mcontent = D("Content");
            $wheres = array();
            $wheres['ncid'] =array('eq', $_data['ncid']);
            $wheres['cpid'] = array('eq', $_data['cpid']);
            $wheres['nid'] = array('eq', $_data['nid']);
            $wheres['title'] = array('eq', $_data['title']);
            $find = $Mcontent->field(" ncntid")->where( $wheres )->find();
            if( $find )
            {
                $res['msg'] = "内容已经存在";
                $res['data'] = $find['ncntid'];
            }
            else
            {
                if( $Mcontent->create( $_data ,3 ) )
                {
                    $cntid = $Mcontent->add();
                    $res['msg'] = 'OK';
                    $res['rcode'] = 1;
                    $res['data'] = $cntid;
                }
                else
                {
                    $res['msg'] = "创建表单失败，错误信息：".$Mcontent->getError();
                }
            }
        }
        return $res;
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
