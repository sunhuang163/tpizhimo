<?php
/*
  banfg56
  2015-1-12
  @@用户管理
*/

class UserModel extends AdvModel {

	protected $_validate=array(
		  array('name','','该分类名称已经存在！',0,'unique',1),
		  array('url','','该访问地址已经存在！',0,'unique',1),
		  array('name','require','小说的名称不能为空!',1),

	);

	protected $_auto=array(
		 array('cn','0'),
		 array('state','1'),
	);

	protected  function _before_insert(&$data,$options){

	}

  /**
   2015/2/13
   banfg56
   @info 图片验证码接口
  */
  public function  caption( $svcode = "vcode" , $conf = NULL )
 {
    $width = '80';
    $height = '30';
    $characters = 5;

    $set_font = APP_PATH.'font/2.ttf';

    $code = $this->_vcode($characters);
   /* font size will be 55% of the image height */
    $font_size = $height * 0.55;
    $image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
    /* set the colors */
    $background_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 41, 85, 68);
    $noise_color = imagecolorallocate($image, 122, 192, 66);
    /* generate random dots in background */
    for ($i = 0; $i < ($width * $height) / 3; $i++) {
    imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 1, 1, $noise_color);
    }
   /* generate random lines in background */
   for ($i = 0; $i < ($width * $height) / 150; $i++) {
   imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $noise_color);
   }
   /* create textbox and add text */
   $textbox = imagettfbbox($font_size, 0, $set_font, $code) or die('Error in imagettfbbox function');
   $x = ($width - $textbox[4]) / 2;
   $y = ($height - $textbox[5]) / 2;
   imagettftext($image, $font_size, 0, $x, $y, $text_color, $set_font, $code) or die('Error in imagettftext function');
   Header("Content-type: image/jpeg");
   Imagejpeg($image);                    //生成png格式
   Imagedestroy($image);
   $_SESSION[$svcode] = $code;
 }



 protected function _vcode($characters = 4){
     $possible = '123456789abcdfghjkmnpqrstvwxyz';
     $code = '';
     $i = 0;
    while ($i < $characters) {
     $code .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
     $i++;
    }
    return $code;
   }

}
?>