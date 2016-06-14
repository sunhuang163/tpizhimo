<?php
namespace app\common\model;

class Tag extends \think\Model
{

	protected $_validate=array(
      array('name','require','标签名称必需存在!'),
	);

	protected $_auto=array(
		array('ctime','time',1,'function'),
	);

    protected $_link = array(
       'tags'=> array(
       'mapping_type'=>HAS_MANY,
                    'class_name'=>'Tagindex',
                    'foreign_key'=>'tid',
                    'mapping_name'=>'tags',
                    'mapping_order'=>'ctime asc',
       ),

	);

    //根据字符串解析标签
	public function  parse($tags ,$nid  , $sep = "|")
	{
		$tags = trim( $tags );
		$arrTag = [];
		$arrTag = explode( '|',$tags );
		$Mtag = \think\Db::name('tag');
		$Mtags =  \think\Db::name('tagindex');
		$wheres = $wheresti = [];
		foreach( $arrTag as $_v)
		{
			if( $_v )
			{
				$_v = strtolower( $_v );
				$wheres['name'] = array('eq',$_v);
				$dtag = array();
				$dtag = $Mtag->where( $wheres )->find();
				if( !$dtag )
				{
					$dtag['name'] = $_v;
					$dtag['tc'] = 1;
					$dtag['ctime'] = time();
					$dtag['tid'] = $Mtag->insert( $dtag );
					$dtindex = array();
					$dtindex['ctime'] = time();
					$dtindex['tid'] = $dtag['tid'];
					$dtindex['nid'] = $nid;
					$Mtags->insert( $dtindex );
				}
				else
				{
					$wheresti['nid'] = array('eq',$nid);
					$wheresti['tid'] = $dtag['tid'];
					$dindex = NULL;
					$dindex = $Mtags->where( $wheresti )->find();
					if( !$dindex )
					{
						$dup = [];
						$wheres['tid'] = array('eq',$dtag['tid']);
						$dup['tc'] =array('exp','tc+1');
						$dtindex = [];
						$dtindex['ctime'] = time();
						$dtindex['tid'] = $dtag['tid'];
						$dtindex['nid'] = $nid;
						$Mtags->insert( $dtindex );
						$Mtag->where( $wheres )->update( $dup );
					} //if !$dindex
				}// else
			}// if
		}//foreach
	}
}