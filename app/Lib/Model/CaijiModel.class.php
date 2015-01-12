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



public function Cate()
{
  $cate = array(
	        array(
            'name' => '玄幻',
	        'tag' => '玄幻|奇幻',
	        'id' => 8
             ),
	        array(
            'name' => '都市',
	        'tag' => '都市',
	        'id' => 9
             ),
	        array(
            'name' => '武侠',
	        'tag' => '武侠|修正|仙侠',
	        'id' => 11
             ),
	        array(
            'name' => '历史',
	        'tag' => '历史|军事',
	        'id' => 12
             ),
	       array(
            'name' => '言情',
	        'tag' => '言情',
	        'id' => 10
             ),
    );
   return $cate;
}

 public  function start( $p = 1 )
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
  if( $matches && isset($matches[2]) ){
     foreach( $matches[1] as $_k=>$_v){
        var_dump( $matches[2][$_k]);
		var_dump( $matches[1][$_k]);
        $this->getNovel( $_v );
			exit();
	 }
  }
  return $nexturl;
}

// images/noimg.gif,无图片

public function getContent( $url = "")
{
  $_cnt = '';
}

public function getNovel( $url = "" )
{
  //$url = "http://www.day66.com/Book/35016.aspx";
  $_cnt = curl_content( $url , 30);
  $cnt = "";
  if( $_cnt)
	  $cnt = g2u( $_cnt );

   $npreg =  array(
	  'title' => "#class=\"booktitle tc b\"><li>《(.*)》<\/li><\/ul>#isU",
	  'author' => "#小说\">(.*)<\/a><\/li><li\sclass=\"li11\">作品性质<#isU",
	  'img' => "#\"bookimg fl\"><img\sclass=\"img\"\ssrc=\"(.*)\"#isU",
	  'cate' => "#LC\/\d+\.aspx\">(.*)<\/a><\/li><li\sclass=\"li11\">所属文集<\/li>#iU",
      'tags' => "^LN\/\d+\.aspx\">(.*)<\/a><\/li><li\s+class=\"li11\">授权状态<^iU",
      'utime' => "#更新时间<\/li><li class=\"li12\">&nbsp;(.*)<\/li>#isU",
	  'ndesc' => "#<!--简介开始-->(.*)<!--简介结束-->#isU",
    );
   preg_match( $npreg['img'] , $cnt ,$match);
   var_dump( $match );
 }

}
?>