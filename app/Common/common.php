<?php
  function md6( $str ){
    return md5(md5( md5($str) ));
  }

/*----- File Operation ----*/
 function mkdirss($dirs,$mode=0777) {
	if(!is_dir($dirs)){
		mkdirss(dirname($dirs), $mode);
		return @mkdir($dirs, $mode);
	}
	return true;
}


/*----- String/Charset Operation -----*/
function u2g($str){
	return iconv("UTF-8","GBK",$str);
}

function g2u($str){
	return iconv("GBK","UTF-8//ignore",$str);
}

function t2js($l1, $l2=1){
    $I1 = str_replace(array("\r", "\n"), array('', '\n'), addslashes($l1));
    return $l2 ? "document.write(\"$I1\");" : $I1;
}


function nr($str){
	$str = str_replace(array("<nr/>","<rr/>"),array("\n","\r"),$str);
	return trim($str);
}

function nb($str){
	$str = str_replace("　",' ',str_replace("&nbsp;",' ',$str));
	$str = ereg_replace("[\r\n\t ]{1,}",' ',$str);
	return trim($str);
}


function msubstr($str, $start=0, $length, $suffix=false){
	return ff_msubstr(eregi_replace('<[^>]+>','',ereg_replace("[\r\n\t ]{1,}",' ',nb($str))),$start,$length,'utf-8',$suffix);
}
function ff_msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){
	$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all($re[$charset], $str, $match);
	$length_new = $length;
	for($i=$start; $i<$length; $i++){
		if (ord($match[0][$i]) > 0xa0){
			//中文
		}else{
			$length_new++;
			$length_chi++;
		}
	}
	if($length_chi<$length){
		$length_new = $length+($length_chi/2);
	}
	$slice = join("",array_slice($match[0], $start, $length_new));
    if($suffix && $slice != $str){
		return $slice."…";
	}
    return $slice;
}


function ff_pinyin($str,$ishead=0,$isclose=1){
	$str = u2g($str);
	global $pinyins;
	$restr = '';
	$str = trim($str);
	$slen = strlen($str);
	if($slen<2){
		return $str;
	}
	if(count($pinyins)==0){
		$fp = fopen('pinyin.dat','r');
		while(!feof($fp)){
			$line = trim(fgets($fp));
			$pinyins[$line[0].$line[1]] = substr($line,3,strlen($line)-3);
		}
		fclose($fp);
	}
	for($i=0;$i<$slen;$i++){
		if(ord($str[$i])>0x80){
			$c = $str[$i].$str[$i+1];
			$i++;
			if(isset($pinyins[$c])){
				if($ishead==0){
					$restr .= $pinyins[$c];
				}
				else{
					$restr .= $pinyins[$c][0];
				}
			}else{
				//$restr .= "_";
			}
		}else if( eregi("[a-z0-9]",$str[$i]) ){
			$restr .= $str[$i];
		}
		else{
			//$restr .= "_";
		}
	}
	if($isclose==0){
		unset($pinyins);
	}
	return $restr;
}

function ff_letter_first($s0){
	$firstchar_ord=ord(strtoupper($s0{0}));
	if (($firstchar_ord>=65 and $firstchar_ord<=91)or($firstchar_ord>=48 and $firstchar_ord<=57)) return $s0{0};
	$s=iconv("UTF-8","gb2312", $s0);
	$asc=ord($s{0})*256+ord($s{1})-65536;
	if($asc>=-20319 and $asc<=-20284)return "A";
	if($asc>=-20283 and $asc<=-19776)return "B";
	if($asc>=-19775 and $asc<=-19219)return "C";
	if($asc>=-19218 and $asc<=-18711)return "D";
	if($asc>=-18710 and $asc<=-18527)return "E";
	if($asc>=-18526 and $asc<=-18240)return "F";
	if($asc>=-18239 and $asc<=-17923)return "G";
	if($asc>=-17922 and $asc<=-17418)return "H";
	if($asc>=-17417 and $asc<=-16475)return "J";
	if($asc>=-16474 and $asc<=-16213)return "K";
	if($asc>=-16212 and $asc<=-15641)return "L";
	if($asc>=-15640 and $asc<=-15166)return "M";
	if($asc>=-15165 and $asc<=-14923)return "N";
	if($asc>=-14922 and $asc<=-14915)return "O";
	if($asc>=-14914 and $asc<=-14631)return "P";
	if($asc>=-14630 and $asc<=-14150)return "Q";
	if($asc>=-14149 and $asc<=-14091)return "R";
	if($asc>=-14090 and $asc<=-13319)return "S";
	if($asc>=-13318 and $asc<=-12839)return "T";
	if($asc>=-12838 and $asc<=-12557)return "W";
	if($asc>=-12556 and $asc<=-11848)return "X";
	if($asc>=-11847 and $asc<=-11056)return "Y";
	if($asc>=-11055 and $asc<=-10247)return "Z";
	return 0;//null
}

function pagestr( $pnow , $pall , $url ,$psize = 15, $em = 3)
{
  $p = 1;
  $pstr = '<ul>';
  if( $pnow > $pall )
	  $pnow = $pall;
  $cpre = $pnow -1;
  $cnext = $pall - $pnow;

  $pstrnow = '';
  $pstrpre = '';
  $pstrnext = '';
  if( 1 == $pnow )
	  $pstrpre = '<li class="disabled"><a href="javascript:void(0);">&laquo;</a></li>';
  else
    $pstrpre = '<li><a href="'.str_replace('{!page!}', $pnow -1 ,$url).'"  >&laquo;</a></li>';

  if( 0 == $cnext )
    $pstrnext = '<li class="disabled"><a href="javascript:void(0);">&raquo;</a></li>';
  else
     $pstrnext = '<li><a href="'.str_replace('{!page!}',$pnow + 1,$url).'" >&raquo;</a></li>';

  if( $cpre > $em){
	 $page =1;
	 for( $page =1 ;$page < $em;$page++){
	   $pstrnow .= '<li><a href="'.str_replace('{!page!}',$page,$url).'" >'.$page.'</a></li>';
	 }
	 $pstrnow.='<li><a href="javacript:void(0);">...</a></li>';
	 $pstrnow .= '<li><a href="'.str_replace('{!page!}',$pnow-1,$url).'"  >'.($pnow-1).'</a></li>';
     $pstrnow .= '<li class="active"><a href="'.str_replace('{!page!}',$pnow,$url).'" >'.$pnow.'</a></li>';
  }
  else{
	for( $page =1 ;$page < $pnow;$page++){
	   $pstrnow .= $strpre = '<li><a href="'.str_replace('{!page!}',$page,$url).'" >'.$page.'</a></li>';
	 }
	 $pstrnow .= '<li class="active" ><a href="'.str_replace('{!page!}',$pnow,$url).'" >'.$pnow.'</a></li>';
  }

 if( $cnext > $em )
 {
    $page =1 + $pnow;
	 for( ;($page < $pnow+$em);$page++){
	   $pstrnow .= '<li><a href="'.str_replace('{!page!}',$page,$url).'" >'.$page.'</a></li>';
	 }
	 $pstrnow.='<li><a href="javacript:void(0);">...</a></li>';
	  $pstrnow .= '<li><a href="'.str_replace('{!page!}',$pall,$url).'" >'.$pall.'</a></li>';
 }
 else
 {
    $page =1 + $pnow;
	 for( ;($page <= $pall);$page++){
	   $pstrnow .= '<li><a href="'.str_replace('{!page!}',$page,$url).'" >'.$page.'</a></li>';
	 }
 }
  $pstr.= $pstrpre.$pstrnow.$pstrnext.'</ul>';
 return $pstr;
}

/*----- Content collect -------*/
 function curl_content($url,$timeout=10,$referer){

	if(function_exists('curl_init')){
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,$timeout);
		if($referer){
			curl_setopt ($ch, CURLOPT_REFERER, $referer);
		}
		$content = curl_exec($ch);
		curl_close($ch);
		if($content){
			return $content;
		}
	}
	$ctx = stream_context_create(array('http'=>array('timeout'=>$timeout)));
	$content = @file_get_contents($url, 0, $ctx);
	if($content){
		return $content;
	}
	return false;
}


function ff_upload( $fkey = 'upfile')
{
  $Finfo = array(
    'rcode' => -1,
	 'msg' => '服务器忙',
	'name' => '',
	'type' => '',
	 'size' => 0,
    'file_path' => '',
    'view_path'  => ''
  );
  $updir = C('U_UPLOAD_DIR').date( C('U_UPLOAD_DIRPATH'));
  $reldir = __ROOT__;
  $reldir = realpath( $reldir );
  $reldir = str_replace("\\","/",$reldir).'/';
  $reldir = str_replace("//","/",$reldir);

  if( isset( $_FILES[$fkey]) && is_uploaded_file($_FILES[$fkey]['tmp_name']))
  {
    if(( UPLOAD_ERR_OK != $_FILES[$fkey]['error']) || ($_FILES[$fkey]['size'] == 0 ) ){
	  $Finfo['ff'] = $_FILES[$fkey];
	 $Finfo['msg'] = '上传文件错误';
	}
	else
   {
     mkdirss($reldir.$updir);
	 $_fname = basename($_FILES[$fkey]['name']);
     $fname = uniqid();
	 $fname.=substr($_fname,strrpos($_fname,'.'));
	 $desfile =$reldir.$updir.$fname;
	 if( !move_uploaded_file( $_FILES[$fkey]['tmp_name'] , $desfile ) )
	 {
		 $Finfo['msg'] = '移动临时文件失败';
	 }
	 else
	{
      @unlink( $_FILES[$fkey]['tmp_name']);
      $MAtt = D('Att');
	  $datt = array();
	  $datt['name'] = $updir.$fname;
      $datt['ext'] =substr($_fname,strrpos($_fname,'.')+1);
	  $datt['ctime'] = time();
      $datt['atype'] = $MAtt->ftype( $datt['ext'] );
	  $datt['size'] = filesize($reldir.$updir.$fname );
	  //图片缩略图,水印
	  if( 'img' == $datt['atype']){
		  mkdirss($reldir.$updir.'thumb/',755);
		  import('ORG.Util.Image');
		  Image::thumb( $reldir.$updir.$fname , $reldir.$updir.'thumb/'.$fname,'',200 ,150,true);
        if(  C('IMG_WATER') ){
		   import('ORG.Util.Image');
		   Image::water( $reldir.$updir.$fname ,C('IMG_WATER_PIC'));
		 }
	  }
	  $MAtt->data( $datt )->add();
	  $Finfo['rcode'] = 1;
	  $Finfo['name'] = $fname;
	  $Finfo['type'] = $datt['atype'];
	  $Finfo['size'] = $datt['size'];
	  $Finfo['ss'] = __ROOT__.C('IMG_WATER_PIC');
	  $Finfo['file_path'] = $updir.$fname;
	  $Finfo['view_path'] = C('SITE_URL').$updir.$fname;
	  $Finfo['msg'] = '文件上传成功';
	}
   }
  }
  else
  {
   $Finfo['msg'] = '上传文件不存在';
  }
  return $Finfo;
}

	//远程下载图片
 function down_img($url,$sid='vod'){
       $chr = strrchr($url,'.');
	   $imgUrl = uniqid();
	   $imgPath = $sid.'/'.date(C('upload_style'),time()).'/';
	   $imgPath_s = './'.C('upload_path').'-s/'.$imgPath;
	   $filename = './'.C('upload_path').'/'.$imgPath.$imgUrl.$chr;
	   $get_file = ff_file_get_contents($url);
	   if ($get_file) {
		   write_file($filename,$get_file);
		   //是否添加水印
		   if(C('upload_water')){
			   import('ORG.Util.Image');
			   Image::water($filename,C('upload_water_img'),'',C('upload_water_pct'),C('upload_water_pos'));
		   }
		   //是否生成缩略图
		   if(C('upload_thumb')){
			   mkdirss($imgPath_s);
			   import('ORG.Util.Image');
			   Image::thumb($filename,$imgPath_s.$imgUrl.$chr,'',C('upload_thumb_w') ,C('upload_thumb_h'),true);
		   }
		   //是否上传远程
		   if (C('upload_ftp')) {
			   $this->ftp_upload($imgPath.$imgUrl.$chr);
		   }
		   return $imgPath.$imgUrl.$chr;
	   }else{
			return $url;
	   }
	}

	//远程ftp附件
   function ftp_upload($imgurl, $isIMG = FALSE , $thumb = 'thumb/'){
		Vendor('Ftp.Ftp');
		$ftpcon = array(
			'ftp_host'=>C('upload_ftp_host'),
			'ftp_port'=>C('upload_ftp_port'),
			'ftp_user'=>C('upload_ftp_user'),
			'ftp_pwd'=>C('upload_ftp_pass'),
			'ftp_dir'=>C('upload_ftp_dir'),
		);
		$ftp = new ftp();
		$ftp->config($ftpcon);
		$ftp->connect();
		$ftpimg = $ftp->put(C('upload_path').'/'.$imgurl,C('upload_path').'/'.$imgurl);
		if(C('upload_thumb')){
			//$imgurl_s = strrchr($imgurl,"/");
			//$ftpimg_s = $ftp->put(C('upload_path').'/thumb'.$imgurl_s, 'thumb'.$imgurl_s);
			$ftpimg_s = $ftp->put(C('upload_path').'-s/'.$imgurl, C('upload_path').'-s/'.$imgurl);
		}
		if(C('upload_ftp_del')){
			if($ftpimg){
				@unlink(C('upload_path').'/'.$imgurl);
			}
			if($ftpimg_s){
				@unlink(C('upload_path').'/thumb'.$imgurl_s);
			}
		}
		$ftp->bye();
   }


/*------ Encrpt --------*/
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key != '' ? $key :  C('U_HASH_KEY') );
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}}

function remove_xss($val) {
   $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
   $search = 'abcdefghijklmnopqrstuvwxyz';
   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $search .= '1234567890!@#$%^&*()';
   $search .= '~`";:?+/={}[]-_|\'\\';
   for ($i = 0; $i < strlen($search); $i++) {
      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
   }
   $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
   $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   $ra = array_merge($ra1, $ra2);
   $found = true; // keep replacing as long as the previous round replaced something
   while ($found == true) {
      $val_before = $val;
      for ($i = 0; $i < sizeof($ra); $i++) {
         $pattern = '/';
         for ($j = 0; $j < strlen($ra[$i]); $j++) {
            if ($j > 0) {
               $pattern .= '(';
               $pattern .= '(&#[xX]0{0,8}([9ab]);)';
               $pattern .= '|';
               $pattern .= '|(&#0{0,8}([9|10|13]);)';
               $pattern .= ')*';
            }
            $pattern .= $ra[$i][$j];
         }
         $pattern .= '/i';
         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
         if ($val_before == $val) {
            // no replacements were made, so exit the loop
            $found = false;
         }
      }
   }
   return $val;
}

function getip(){
   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
       $ip = getenv("HTTP_CLIENT_IP");
   else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
       $ip = getenv("HTTP_X_FORWARDED_FOR");
   else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
       $ip = getenv("REMOTE_ADDR");
   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
       $ip = $_SERVER['REMOTE_ADDR'];
   else
       $ip = "unknown";
   return($ip);
}

function h($text, $tags = null){
	$text	=	trim($text);
	$text	=	preg_replace('/<!--?.*-->/','',$text);
	$text	=	preg_replace('/<\?|\?'.'>/','',$text);
	$text	=	preg_replace('/<script?.*\/script>/','',$text);

	$text	=	str_replace('[','&#091;',$text);
	$text	=	str_replace(']','&#093;',$text);
	$text	=	str_replace('|','&#124;',$text);
	$text	=	preg_replace('/\r?\n/','',$text);
	$text	=	preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
	$text	=	preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
	while(preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1],$text);
	}
	while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].$mat[3],$text);
	}
	if(empty($tags)) {
		$tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
	}
	$text	=	preg_replace('/<('.$tags.')( [^><\[\]]*)>/i','[\1\2]',$text);
	$text	=	preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i','',$text);
	while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
		$text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
	}
	while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].'|'.$mat[3].'|'.$mat[4],$text);
	}
	while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
		$text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
	}
	$text	=	str_replace('<','&lt;',$text);
	$text	=	str_replace('>','&gt;',$text);
	$text	=	str_replace('"','&quot;',$text);

	$text	=	str_replace('[','<',$text);
	$text	=	str_replace(']','>',$text);
	$text	=	str_replace('|','"',$text);

	$text	=	str_replace('  ',' ',$text);
	return $text;
}

/*---------------后台用户相关 -------------*/
 function salog( $uid , $name , $type = 'NF' ,$msg = 'Nothing'){
     $MLog = M('syslog');
     $Ldata = array();

    $uinfo = isset($_SESSION[C('U_AUTH_KEY')]) ? $_SESSION[C('U_AUTH_KEY')] : '';
    if( count($uinfo) )
     {
		  $uinfos = authcode( $uinfo , "DECODE");
	      $arruinfo = unserialize( $uinfos );
		  $uid = isset( $arruinfo['uid'] ) ? $arruinfo['uid'] : $uid;
		  $name = isset( $arruinfo['name'] ) ? $arruinfo['name'] : $name;
     }
	 $Ldata['said'] = $uid;
	 $Ldata['name'] = $name;
	 $Ldata['ctime'] = time();
	 $Ldata['ctype'] = $type;
	 $Ldata['msg'] = $msg;
	 $Ldata['ip'] = ip2long( get_client_ip() );

	 return $MLog->data( $Ldata )->add();
   }

?>