<?php
/**
 banfg56
 2014/11/27 星期四
 @@富文本编辑器加载Widget
*/
class EditorWidget extends Widget {
      public function render($data){
        $content = $this->renderFile('editor',$data);
      return $content;
   }

}
?>