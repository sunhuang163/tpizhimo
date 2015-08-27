<?php
/**
 *  @Author  banfg56
 *  @Date    2015-1-8
 *  @Info  小说分类查看
 *  @File   CateAction.class.php
 *
 */
class CateAction extends HomeAction {

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
                $this->assign("ncid" , $d['ncid']);
                $this->assign("cate" , $d );
                $this->display();
            }

        }
    }

    //字母分页
    public function zimu()
    {
        $zimu = isset( $_REQUEST['zimu'] ) ? trim( $_REQUEST['zimu'] ) : 'A';
        $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
        $zimu = strtoupper( $zimu );
        $this->assign("p", $p);
        $this->assign("zimu", $zimu);
        $this->display();
    }

    public function  show()
    {
        $Mcate = M("Nclass");
        $wheren = array();
        $cate = isset( $_REQUEST['url']) ? trim( $_REQUEST['url']) : "";
        $p = isset( $_REQUEST['p']) ? intval( $_REQUEST['p']) : 1;
        $wheren['url'] = array('eq' , $cate);
        $dcate = $Mcate->where( $wheren )->find();
        $this->assign("ncid" , $dcate['ncid']);
        $this->assign("p" , $p );
        $this->assign("dcate" , $dcate );
        $this->display();
    }

}
?>