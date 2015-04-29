<?php
/*----
   banfg56
   2015-1-8
   @@小说分类查看
----*/
class CateAction extends BaseAction {

    public function index()
    {
        $Mdo = M("Nclass");
        $wheres = array();
        $url = isset( $_GET['url']) ? trim( $_GET['url']) : "";
        $id = isset(  $_GET['cid']) ? trim( $_GET['cid']) : 0;
        if( !$url && !$id)
        {
            $this->error("访问地址参数错误");
        }
        else
        {
            if( $url )
            {
               $where['url'] = array('eq' , $url); 
            }
            else if( $id )
            {
                $where['ncid'] = array('eq' , $ncid );
            }
            $d = $Mdo->where( $wheres )->find();
            if( !$d )
            {
                $this->error("访问的分类不存在");
            }
            else if( 0 == $d['state'])
            {
                $this->error("该分类已经隐藏");
            }
            else
            {
                $this->assign("ncid" , $d['ncid']);
                $this->assign("cate" , $d );
                $this->display();  
            }
            
        }
    }

    //二级分类的列表页
    public function  show()
    {
        $this->display();
    }

}
?>