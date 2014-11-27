<?php
/**
 banfg56
 2014/11/27 星期四
 @@上传文件Widget
*/
class UploadWidget extends Widget {
      publicfunctionrender($data){
        $content = $this->renderFile('upload',$data);
      return $content;
   }

}
?>