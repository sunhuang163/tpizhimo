<?php
/**
 banfg56
 2014/11/27 星期四
 @@富文本编辑器加载Widget
*/
class EditorWidget extends Widget {
      publicfunctionrender($data){
        $content = $this->renderFile('edit',$data);
      return $content;
   }

}
?>