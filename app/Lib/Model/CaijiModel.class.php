<?php
/*
  banfg56
  2015-1-12
  @@采集管理
*/

class CaijiModel extends Model{
  private $cache_type = "File";
  private $src = "";
  private $caiji_data = NULL;
  private $soso = "http://www.day66.com/Book/ShowBookList.aspx?tclassid=0&nclassid=0&page={!p!}";
   public $decate = array(
	        array('name' => '玄幻奇幻', 'tag' => '玄幻|奇幻', 'id' => 3 ),
	        array('name' => '都市小说',  'tag' => '都市',  'id' => 4 ),
	        array('name' => '武侠修真',  'tag' => '武侠|修正|仙侠',  'id' => 6  ),
	        array('name' => '历史军事',  'tag' => '历史|军事', 'id' => 10   ),
	        array('name' => '女生言情', 'tag' => '言情',  'id' => 5 ),
	        array('name' => '经管励志', 'tag' => '经济|管理|励志',  'id' => 12 ),
	        array('name' => '法律教育', 'tag' => '法律|教育|心理',  'id' => 13 ),
	        array('name' => '文学名著', 'tag' => '文学|名著|古文|经典',  'id' => 7 ),
	        array('name' => '科幻小说', 'tag' => '科幻',  'id' => 8 ),
	        array('name' => '恐怖小说', 'tag' => '恐怖|悬疑|灵异',  'id' => 9)
	       // array('name' => "未分类",  'tag' => '*', 'id' =>1 ),
    );

public function cate( $tag )
{
  $cate = $this->decate;

   foreach( $cate as $v ){
    if( preg_match( "#".$v['tag']."#isU",$tag)){
	 return $v['id'];
	}
   }
   return 1;  //默认是未分类
}

/**
  @function nList
  @access public
  @parameter
     $p int 分页
**/
public function nList( $p = 1 )
{
  $dList = array();
  $url = "";
  $url = str_replace('{!p!}',$p,$this->soso);
  $_cnt = curl_content( $url ,30 );
  $cnt = "";
  if( $_cnt){
   $cnt =g2u( $_cnt );
  }
  $reg="^class=\"fl tl pd8 lm\" style=\"width:50%;\"><a\shref=\"(.*)\"><font color=\"#116699\">(.*)<\/font><\/a>^isU";
  preg_match_all($reg,$cnt,$matches);
  if( $matches && isset($matches[2]) ){
     foreach( $matches[1] as $_k=>$_v){
        $item = array();
		$item['url'] = $_v;
		$item['title'] = $matches[2][$_k];
		$dList[] = $item;
	 }
  }
 return $dList;
}

/**
   @function getContent
   @access  public
   @parameter
      $url string  小说内容页面的地址
**/
public function getContent( $url = "" )
{
  //$url = "http://www.day66.com/xiaoshuo/23/23149/1272529.shtml";
  $_cnt = '';
  $_cnt = curl_content( $url , 30);
  $cnt = "";
  $cnt = g2u( $_cnt );
  $reg = "#<div\s+id=\"htmlContent\"\s+class=\"p\">(.*)<\/div>#isU";
  if( preg_match( $reg , $cnt , $match) ){
      return h(  remove_xss($match[1]) );
  }
  else
	  return "";
}

/**
  @function getChapter
  @access public
  @parameter
       $url string 目录页面地址，内容包括分卷信息和具体的内容信息
**/
public function getChapter( $url = "")
{
   //$url = "http://www.day66.com/xiaoshuo/23/23149/";
   $res = array('rcode'=>0,'msg'=>"服务器忙，请稍后再试",'data'=> NULL);
   $_cnt = '';
   $_cnt = curl_content( $url , 30);
   $cnt = "";
   $cnt = g2u( $_cnt );

   if( $_cnt && $cnt ){
	   $cnt = nb(nr($cnt));
       $nreg = array(
          'chapter' => "#<div class=\"tit\"><h2>(.*)<\/h2>#iU",
          'cnt' => "#<\/h2><\/div> <div class=\"con\">\s+<ul>\s+(.*)<li><\/li><li><\/li><li><\/li>#is",
		  'pod' => "#<li><\/li><li>#i",
	      'url' => "#<a\s+href=\"(.*)\"\s+title=\"更新时间:(.*)\s+更新字数:\d+\">(.*)<\/a><\/li>#isU",
       );

      $match = array();
	 if(  preg_match_all( $nreg['chapter'],$cnt, $match ) )
	 {
	    $res['data']['cp'] = $match[1];
		$dcnt =array();
	    if( preg_match( $nreg['cnt'] , $cnt , $mcnt ) )
	   {
	     $contents = array();
	     $contents =  preg_split($nreg['pod'],$mcnt[1]);
		 $ic = 0;
	    foreach( $contents as $ks=>$vs)
	    {
	     if( preg_match_all($nreg['url'] , $vs ,$mitem ) )
	    {
	     $ic++;
		 $durls = $mitem[1];
		 $dctimes = $mitem[2];
		 $dtitles = $mitem[3];
		 foreach( $durls as $vvk=>$vvs){
		   $ddcnt = array();
		   $ddcnt['url'] = $url.$vvs;
		   $ddcnt['ctime'] = strtotime( $dctimes[$vvk]);
		   $ddcnt['title'] = trim( $dtitles[$vvk] );
		   $dcnt[$ks][] = $ddcnt;
		 }
		}//if novel url infos
	   }//foreach preg_match content
	   $res['data']['cnt'] = $dcnt;
	  }//if novel conntent
	   $res['rcode'] = 1;
	   $res['msg'] = "获取内容成功";
	 } //if chapter
	 else
	 {
       $res["msg"] = "分析章节失败";
	 }
   }
   else{
    $res['msg'] = "抓取网页失败";
   }
   return $res;
}

/**
 @function getNovel
 @access public
 @parameter
     $url string 详情页面地址
**/
public function getNovel( $url = "" )
{
  $res = array(
    'rcode' => 0,
    'msg' => "",
	'data'=>NULL
    );
  $dnovel = array();
  $_POST = array();
  $_cnt = curl_content( $url , 30);
  $cnt = "";
  if( $_cnt)
	  $cnt = g2u( $_cnt );
 if( $cnt )
  {
   $Mnovel = D("Novel");
   $npreg =  array(
	  'title' => "#class=\"booktitle tc b\"><li>《(.*)》<\/li><\/ul>#isU",
	  'author' => "#小说\">(.*)<\/a><\/li><li\sclass=\"li11\">作品性质<#isU",
	  'pic' => "#\"bookimg fl\"><img\sclass=\"img\"\ssrc=\"(.*)\"#isU",
	  'cate' => "#LC\/\d+\.aspx\">(.*)<\/a><\/li><li\sclass=\"li11\">所属文集<\/li>#iU",
      'tags' => "^LN\/\d+\.aspx\">(.*)<\/a><\/li><li\s+class=\"li11\">授权状态<^iU",
      'utime' => "#更新时间<\/li><li class=\"li12\">&nbsp;(.*)<\/li>#isU",
	  'ndesc' => "#<!--简介开始-->(.*)<!--简介结束-->#isU",
    );
   foreach( $npreg as $kp=>$vp)
  {
	$match = array();
	if(preg_match($vp,$cnt,$match)){
	  if( isset( $match[1]) )
     {
	   if( "pic" == $kp)
	    {
		  $picurl = "";
		  if( substr($match[1],-9) == "noimg.gif")
			  $picurl = NULL; //图片为空的话，就不显示图片
		  else
		  $picurl ="http://www.day66.com".$match[1] ;
		  if( $picurl )
		  $dnovel['pic'] = $picurl;
	    }
	   else if( 'tags' == $kp)
	   {
		 //设置小说的分类信息
         $ncid = $this->cate( $match[1]);
	     $dnovel['ncid'] = $ncid;
	     $_POST["tags"] = $match[1];//标签的划分
	    }
	  else
		$dnovel[$kp] = $match[1];
	 } // if isset $match
	} //preg_match
   } // foreach
   $nstate  = preg_match("#\"booktexts\s#isU",$cnt) ? 1: 0;
   $dnovel['nstate'] = $nstate;
   $dnovel['uptxt']  = $nstate ? "已完结":"连载中";
   $dnovel['ctime'] = time();
   $dnovel['caijiurl'] = $url;
   $dnovel = (object) $dnovel;
   $resm = $Mnovel->create( $dnovel , 3);
   $wheres = array();
   $wheres['title'] = array('eq',$dnovel->title );
   $wheres['author'] = array('eq',$dnovel->author);
   $find = $Mnovel->field("nid,pic")->where( $wheres)->find();
   if( $find ){
      $res['msg'] = "该小说已经存在";
	  $res['data'] = $find['nid'];
	  $res['ncid'] = $dnovel->ncid;
      //更新图片
	  $picurl = "";
	  if( !$find['pic'] && $Mnovel->pic ){
         $picurl =  down_img( $Mnovel->pic );
	  if( $Mnovel->pic == $picurl)
		  $picurl="";
	  }
	  $wheren = array();
	  $wheren['nid'] = array( 'eq' , $find['nid']);
	  $dc  = array();
	  if( $picurl )
		  $dc['pic'] = $picurl;
      $dc['utime'] = time();
      $Mnovel->where( $wheren )->save( $dc );
   }
    else if(  !$resm ){
      $res['msg'] = $Mnovel->getError();
    }
    else
   {
	 if( $Mnovel->pic ){
		 $picurl =  down_img( $Mnovel->pic );
	  if( $Mnovel->pic == $picurl)
		  $Mnovel->pic="";
	   else
		   $Mnovel->pic = $picurl;
	 }
	 $res['rcode'] = 1;
     $res['data'] = $Mnovel->add();
	 $res['ncid'] = $dnovel->ncid;
     $res['msg']  = "添加成功";
    }
  }
  else
  {
   $res['msg'] = "抓取网页内容失败";
  }
  return $res;
 }

}
?>