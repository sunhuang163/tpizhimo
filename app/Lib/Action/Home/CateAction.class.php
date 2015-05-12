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
               $wheres['url'] = array('eq' , $url); 
            }
            else if( $id )
            {
                $wheres['ncid'] = array('eq' , $ncid );
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
                $Mdo = D("Hot");
                $_wheres['rtype'] = array('eq', HotModel::HOT_HOME_TXT);
                $_wheres['ih_recommend.ncid'] = array('eq' , $d['ncid']);
                $dhot = $Mdo->field("ih_novel.*,ih_nclass.name as catename")
                            ->join("LEFT JOIN ih_novel on ih_novel.nid=ih_recommend.nid")
                            ->join("LEFT JOIN ih_nclass on ih_nclass.ncid=ih_recommend.ncid")
                            ->where( $_wheres )
                            ->order("ih_recommend.ord ASC")
                            ->limit("6")
                            ->select();
                            
                $this->assign("dhot" , $dhot );
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