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

  /*
    分类信息
  */
  private $url_config = array(

   );
 /* function CaijiModel( $src = NULL ,  $url = ""){
	 $this->type = $src;
	 $this->soso = $url;
  } */



public function cate( $tag )
{
  $cate = array(
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

   foreach( $cate as $v ){
    if( preg_match( "#".$v['tag']."#isU",$tag)){
	 return $v['id'];
	}
   }
   return 1;  //默认是未分类
}

 public  function novel( $p = 1 )
{
  $url = str_replace('{!p!}',$p,$this->soso);
  $nexturl = ""; //if empty nexturl,OK  NULL for error
  $_cnt = curl_content( $url ,30 );
  $cnt = "";
  if( $_cnt){
   $cnt =g2u( $_cnt );
  }
  $reg="^class=\"fl tl pd8 lm\" style=\"width:50%;\"><a\shref=\"(.*)\"><font color=\"#116699\">(.*)<\/font><\/a>^isU";
  preg_match_all($reg,$cnt,$matches);
  $ic = 0 ;
  if( $matches && isset($matches[2]) ){
     foreach( $matches[1] as $_k=>$_v){
       $rs = $this->getNovel( $_v );
	   if( $_k > 3)
	   exit();
      if( $rs['data']) $ic++;
	 }
	 if( $rs )
	{
      $p++;
      $nexturl = U("/Admin/Caiji/all",array('p'=>$p));
	}
  }
  else
  {
    if( $_cnt === FALSE)
		$nexturl = NULL;
  }
  return $nexturl;
}

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

/*
 采集时，注意图片
*/
public function getChapter( $url = "")
{
   //$url = "http://www.day66.com/xiaoshuo/23/23149/";

   $res = array('rcode'=>0,'msg'=>"服务器忙，请稍后再试",'data'=> NULL);
   $_cnt = '';
   $_cnt = curl_content( $url , 30);
   $cnt = "";
   $cnt = g2u( $_cnt );

   if( $_cnt && $cnt ){
       $nreg = array(
          'chapter' => "#<div\sclass=\"tit\"><h2>(.*)<\/h2>#i",
          'cnt' => "#class=\"con\">(.*)<\/li><li><\/li><li><\/li>#isU",
	      'url' => "#<li><a\s+href=\"(.*)\"\s+title=\"更新时间:(.*)\s+更新字数:\d+\">(.*)<\/a><\/li>#isU",
       );

      $match = array();
	 if(  preg_match_all( $nreg['chapter'],$cnt, $match ) )
	 {
	    $res['data']['cp'] = $match[1];
		$dcnt =array();
	    if( preg_match_all( $nreg['cnt'] , $cnt , $match ) )
	   {
	     $contents = array();
	     $contents = $match[1];
	    foreach( $contents as $ks=>$vs)
	    {
	     $dcnt = array();
	     preg_match_all($nreg['url'] , $vs ,$match );
		 $dcnt['url'] = $match[1];
	     $dcnt['ctime'] = $match[2];
		 $dcnt['title'] = $match[3];
		 foreach( $dcnt['url'] as &$_vurl){
		  $_vurl = $url.$_vurl;
		 }
		 foreach( $dcnt['ctime'] as &$_vtime){
		  $_vtime = strtotime( $_vtime );
		 }
		 $res['data']['cnt'][$ks] = $dcnt;
		 var_dump( $dcnt );
		 //匹配所有的地址
	    }//foreach
	   }//if cnt
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

public function getNovel( $url = "" )
{
  $res = array(
    'rcode' => 0,
    'msg' => "",
	'data'=>NULL
    );
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
		  if( $match[1] == "/images/noimg.gif")
			  $picurl = NULL; //图片为空的话，就不显示图片
		  else
		  $picurl = down_img( "http://www.day66.com".$match[1] );
		  $_POST['pic'] = $picurl;
	    }
	   else if( 'tags' == $kp)
	   {
		 //设置小说的分类信息
         $ncid = $this->cate( $match[1]);
	     $_POST['ncid'] = $ncid;
	     $_POST["tags"] = $match[1];
	    }
	  else
		$_POST[$kp] = $match[1];
	 } // if isset $match
	} //preg_match
   } // foreach
   $nstate  = preg_match("#\"booktexts\s#isU",$cnt) ? 1: 0;
   $_POST['nstate'] = $nstate;
   $_POST['uptxt']  = $nstate ? "已完结":"连载中";
   $_POST['ctime'] = time();
   $_POST['caijiurl'] = $url;
   $resm = $Mnovel->create();
   $wheres = array();
   $wheres['title'] = array('eq',$Mnovel->title );
   $wheres['author'] = array('eq',$Mnovel->author);
   if( !$resm )
   {
      $res['msg'] = $Mnovel->getError();
   }
   else if( $find = $Mnovel->field("nid")->where( $wheres)->find() ){
    $res['msg'] = "该小说已经存在";
	$res['data'] = 1;
	$wheren = array();
	$wheren['nid'] = array( 'eq' , $find['nid']);
	$dc  = array();
    $dc['utime'] = time();
    $Mnovel->where( $wheren )->save( $dc );
   }
   else{
	$res['rcode'] = 1;
    $res['data'] = $Mnovel->add();
    $res['msg']  = "添加成功";
   }
  }
  else
 {
   $res['msg'] = "抓取网页内容失败";
  }
   //如果更新成功，则更新章节内容信息
  if( $res['data'])
  {
   if( preg_match('#Book\/(.*)\.aspx#isU',$url,$match) ){
      $bookid = $match[1] ;
      $contents = array();
	  $contents = $this->getChapter("http://www.day66.com/xiaoshuo/".substr($bookid,0,2)."/".$bookid."/");
	  echo "<h2>Chapter</h2>";
	  var_dump( $url );
	  var_dump( "http://www.day66.com/xiaoshuo/".substr($bookid,0,2)."/".$bookid."/" );
	  print_r( $contents );
	  exit();
   }
  }
  return $res;
 }

}
?>