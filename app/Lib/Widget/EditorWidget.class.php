<?php
/**
 banfg56
 2014/11/27 星期四
 @@富文本编辑器加载Widget
*/
class EditorWidget extends Widget {
      public function render($data)
	   {
         $deconf = array(
          'items' => "'fullscreen', 'preview', 'source', '|', 'undo', 'redo', '|' , 'code', 'cut', 'copy', 'paste',
        'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
        'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
        'superscript', 'clearhtml', 'quickformat', 'selectall', '/',
        'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
        'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'insertfile', 'table', 'hr', 'baidumap', 'pagebreak',
        'anchor', 'link', 'unlink', '|', 'about'",
         'filterMode' => 'true',
          'allowUpload' => 'true',
		  'allowFileManager' => 'false',
		  'imageUploadJson' => U('/Admin/Att/swf'),
		  'uploadJson' => U('/Admin/Att/swf'),
		  'aid' => '',
		   'filePostName' => 'swffile',
		 );
        $uinfo = isset($_SESSION[C('U_AUTH_KEY')]) ? $_SESSION[C('U_AUTH_KEY')] : '';
		$uid = 0;
         if( count($uinfo) )
        {
		  $uinfos = authcode( $uinfo , "DECODE");
	      $arruinfo = unserialize( $uinfos );
		  $uid = isset( $arruinfo['uid'] ) ? $arruinfo['uid'] : $uid;
        }
	    $deconf['aid'] = authcode( $uid ,"ENCODE");
		$content = $this->renderFile('editor', array_merge($deconf,$data) );
      return $content;
   }

}
?>