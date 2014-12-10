<?php
/**
 banfg56
 2014/11/27 星期四
 @@上传文件Widget,swf上传文件
*/
class UploadWidget extends Widget {
      public function render( $data )
		{
		  $deconf =   array(
		   'aid' =>'' ,
		   'FILE_NAME' => 'swffile',
		   'FILE_LIMIT' => '2MB',
		   'FILE_TYPE' => '*.jpg;*.gif;*.png;*.jpeg',
		   'FILE_DESC' => '图片文件',
           'PATH' => '/public/',
		   'UPLOAD_URL' => U('/Admin/Att/swf'),
		   'BTN_ID' => 'swfupload',
		   'BTN_W' => 140,
		   'BTN_H' =>22 ,
		   'preview' => '',
		   'target' => '',
		   'error' => ''
		  );
		//获取用户ID
        $uinfo = isset($_SESSION[C('U_AUTH_KEY')]) ? $_SESSION[C('U_AUTH_KEY')] : '';
		$uid = 0;
      if( count($uinfo) )
     {
		  $uinfos = authcode( $uinfo , "DECODE");
	      $arruinfo = unserialize( $uinfos );
		  $uid = isset( $arruinfo['uid'] ) ? $arruinfo['uid'] : $uid;
     }
	  $deconf['aid'] = authcode( $uid ,"ENCODE");
        $content = $this->renderFile('upload', array_merge( $deconf ,$data));
      return $content;
   }

}
?>