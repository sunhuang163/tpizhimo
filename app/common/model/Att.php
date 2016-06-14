<?php
namespace app\common\model;

class Att extends \think\Model
{
	//自动验证
	protected $_validate=array(
		array('name','require','文件名必须存在'), //默认情况下用正则进行验证
	);
	//自动完成
	protected $_auto=array(
		array('ctime','time',1,'function'),
	);

     public function fsize( $size ){
       $hsize = '';
      return $hsize;
	 }
	 public function ftype( $type ){
	  $ctype = strtolower( $type );
	  $types  = array(
		       'txt' => '文本',
		       'img' => '图片',
		       'doc' =>  '文档',
		       'file' => '文件',
		       'OT' => '其他'
	           );
     switch( strtolower( $ctype )){
       case 'txt':
	   case 'css':
	   case  'js':
           return 'txt';
		   break;
	   case 'png':
	   case 'jpg':
       case 'jpeg':
	   case 'gif':
		  return 'img';
	    break;
	   case 'doc':
	   case 'pdf':
	   case 'vsd':
	   case 'xsl':
		  return 'doc';
	     break;
       case 'tar':
	   case 'gz':
	   case 'rar':
	   case '7z':
	   case 'bz2':
		 return 'file';
		  break;
	default:
		 return 'OT';
	  break;
	 }
	}

}
?>