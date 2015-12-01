<?php
/**
 *  小说采集Model,自动解析封面图片地址
 *
 * @author banfg56
 * @date   2014-11-28
*/

class NovelModel extends RelationModel {
     const NOVEL_UPDATE = 0 ;/* 小说正在更新中 */
	 const NOVEL_FINISH = 1; /* 小说已经完结 */

	protected $_validate=array(
		  array('title','require','小说标题不能为空!',1),
		  array('ncid','require','小说的分类不能为空!',1,'',1),
		  array('title,author', 'onenovel', '该小说已经存在', 1,'callback', 1),

	);
	//自动完成
	protected $_auto=array(
		array('ctime','time',1,'function'),
	);

    protected function onenovel( $data )
    {
        $wherenovel = array();
        $wherenovel['title'] = array('eq',$data['title']);
        $wherenovel['author'] = array('eq',$data['author']);
        if($this->where($wherenovel )->find())
            return FALSE;
        else
            return TRUE;
    }

    protected function  _before_insert(&$data,$options)
    {
        $data['ctime'] = time();
        if( !$data['utime'])
            $data['utime'] = time();
        else
        {
            if( strtotime( $data['utime']))
        	   $data['utime'] = strtotime( $data['utime']);
        }
        if( $data['pic'] && preg_match('#^http:#isU',$data['pic']))
            $data['pic'] = down_img( $data['pic']);
        if( !$data['url'] )
        {
            $url = ff_pinyin( $data['title']);
            $wheres = array();
            $wheres['url'] = array('eq',$url);
            $Mnovel = M("Novel");
            $cnt = 0;
            $cnt = $Mnovel->where( $wheres )->count();
            if( $cnt )
            	$url .= $cnt;
            $data['url'] = $url;
        }
        if( !$data['zimu'])
        {
            $data['zimu'] = ff_letter_first( $data['title']);
        }
    }

    //分类文章增加,标签解析，分类计数增加,小说数据添加
    protected function _after_insert($data,$options)
    {
        $tags = isset( $_POST['tags']) ? trim( $_POST['tags']) :'';
        if( $tags )
        {
            $Mtag = D("Tag");
            $Mtag->parse($tags , $data['nid']);
        }
        $Mclass  = M("Nclass");
        $wheres = array();
        $wheres['ncid'] = array('eq',$data['ncid']);
        $d = array();
        $d['cn'] =array('exp','cn+1');
        $Mclass->where( $wheres )->save( $d );
        $Mndata = M("Ndata");
        $nd  = array();
        $nd['nid'] = $data['nid'];
        $Mndata->data( $nd )->add();
    }

    protected function _before_update(&$data,$options)
    {
        //
    }

    protected function _after_update($data,$options)
    {

    }

}
?>