<?php
/**
 banfg56
 2014/11/27 星期四
 @@上传文件Widget,swf上传文件
*/
 namespace app\admin\widget;

use app\common\controller\Base;

class Wall extends Base
{
    public function upload( $FILE_NAME = 'swffile', $FILE_LIMIT = '2MB', $FILE_TYPE = '*.jpg;*.gif;*.png;*.jpeg',$FILE_DESC = '图片文件', $PATH = '/public/', $BTN_W = 140, $BTN_H = 22 , $BTN_ID = 'swfupload', $preview = '', $target = '', $error ='', $UPLOAD_URL = '')
	{
		$data = [];
		$data['FILE_NAME'] = $FILE_NAME;
		$data['FILE_LIMIT'] = $FILE_LIMIT;
		$data['FILE_TYPE'] = $FILE_TYPE;
		$data['FILE_DESC'] = $FILE_DESC;
		$data['PATH'] = $PATH;
		$data['UPLOAD_URL'] = $UPLOAD_URL ?  $UPLOAD_URL : url('/admin/att/swf');
		$data['BTN_ID'] = $BTN_ID;
		$data['BTN_W'] = $BTN_W;
		$data['BTN_H'] = $BTN_H;
		$data['preview'] = $preview;
		$data['target'] =  $target;
		$data['error'] = $error;
	  	$data['aid'] = authcode($this->a_u['uid'],'ENCODE');
	  	$this->assign( $data );
	  	$content = '';
        $content = $this->fetch("Public/upload");
      	echo $content;
   }

    public function editor( $items = "'fullscreen', 'preview', 'source', '|', 'undo', 'redo', '|' , 'code', 'cut', 'copy', 'paste',
						            'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
						            'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
						            'superscript', 'clearhtml', 'quickformat', 'selectall', '/',
						            'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
						            'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'insertfile', 'table', 'hr', 'baidumap', 'pagebreak',
						            'anchor', 'link', 'unlink', '|', 'about'" , $filterMode = 'true', $allowUpload = 'true',$allowFileManager = 'false', $filePostName = 'swffile',
						            $imageUploadJson = '',  $uploadJson = '')
	{
		$data = [];
		$data['imageUploadJson'] = $imageUploadJson ? $imageUploadJson : url('/admin/att/swf');
		$data['uploadJson'] = $uploadJson ? $uploadJson : url('/admin/att/swf');
		$data['items'] = $items;
		$data['filterMode'] = $filterMode;
		$data['allowUpload'] = $allowUpload;
		$data['allowFileManager'] = $allowFileManager;
		$data['filePostName'] = $filePostName;
	    $data['aid'] = $this->a_u['uid'];
        $content = $this->fetch("Public/editor");
      	echo $content;
   }
}
?>