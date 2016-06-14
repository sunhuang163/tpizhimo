<?php
/***
* @Author  banfg56
* @Date    2015-05-21
* @Info    将网站内容生成静态html
* @File    HtmlAction.class.php
*
*/

namespace app\admin\controller;

use app\common\controller\Base;

class Html extends Base
{
    private $m_htmlDir = '';
    private $m_baseDir = "";

    public function _initialize()
    {
        parent::_initialize();
        $htmldir = C('U_HTML_DIR');
        $reldir = __ROOT__;
        $reldir = realpath( $reldir );
        $reldir = str_replace("\\","/",$reldir).'/';
        $reldir = str_replace("//","/",$reldir);
        $this->m_baseDir = $reldir ;
        $this->m_htmlDir = $reldir. $htmldir ;
    }

	public function index()
    {
	    return $this->fetch();
	}
    //创建首页静态页面
    public function createHome()
    {
        $homeUrl = U("/Home/Index/index", NULL , "", FALSE , TRUE );
        $nextAction = isset( $_GET['next'] ) ? trim( $_GET['next']) : "";
        $fileContent = curl_content( $homeUrl );
        if( $fileContent )
        {
            write_file( $this->m_baseDir.'index.html', nb( $fileContent ) );
            $url = "";
            if( $nextUrl )
                $url = U("/Home/Html/".$nextAction, NULL , "", FALSE , TRUE );
            else
                $url = U('/Admin/Index/index');
            $this->assign("jumpUrl" , $url );
            $this->success("首页静态页面生成成功");
        }
        else
        {
            $this->assign("jumpUrl" , U('/Admin/Index/index'));
            $this->error("首页生成错误");
        }
    }

    //创建最新更新，排行，完结等
    public function createFinish()
    {
        $nextAction = isset( $_GET['next'] ) ? trim( $_GET['next']) : "";
        $fileUrl = U("/Home/Index/rank", NULL , "", FALSE , TRUE );
        $fileContent = curl_content( $fileUrl );
        mkdirss( $this->m_htmlDir  );
        if( $fileContent )
        {
            write_file( $this->m_htmlDir.'rank.html', nb( $fileContent ) );
        }
        $fileUrl = U("/Home/Index/finish", NULL , "", FALSE , TRUE );
        $fileContent = curl_content( $fileUrl );
         if( $fileContent )
        {
            write_file( $this->m_htmlDir.'finish.html', nb( $fileContent ) );
        }
        $fileUrl = U("/Home/Index/update", NULL , "", FALSE , TRUE );
        $fileContent = curl_content( $fileUrl );
         if( $fileContent )
        {
            write_file( $this->m_htmlDir.'update.html', nb( $fileContent ) );
        }
        $url = "";
        if( $nextUrl )
            $url = U("/Home/Html/".$nextAction, NULL , "", FALSE , TRUE );
        else
            $url = U('/Admin/Index/index');
        $this->assign("jumpUrl" , $url );
        $this->success("最近更新生成成功!");
    }

    //创建列表页首页静态页面
    public function createCate()
    {
        //code here
    }

    //创建分类列表页
    public function createList()
    {
        //code here
    }

    //创建某个内容静态页面
    public function createNovel()
    {
        $ids = isset( $_REQUEST['ids']) ? trim( $_REQUEST['ids']) : '';
        //生成指定内容的小说
        if( $ids )
        {
            //
            var_dump( $ids );
        }
        else
        {
            //分页生成静态页面
            $p = isset( $_GET['p']) ? intval( $_GET['p']) : 1; //默认为第一页,按照更新时间排序

        }
    }


    //谷歌  sitemap
    public function siteMap()
    {
        //页面改变频率 hourly,daily,weekly  页面权重 1最大
        $urls = array();
        $uItem = array(
            'loc'=>'',
            'lastmod'=> date('Y-m-d H:i:s'),
            'changefreq'=>'hourly',
            'priority' =>'1',
            );
        $uItem['loc'] = C('SITE_URL');
        $urls[] = $uItem;

        //最近更新、完结、排行

        //分类首页

        //详情页面

        $this->assign("urls" , $urls );
        $cnt = $this->fetch("Html:siteMap");
        write_file( $this->m_baseDir.'sitemap.xml', nb( $cnt ) );
        $nextAction = isset( $_GET['next'] ) ? trim( $_GET['next']) : "";
         $url = "";
        if( $nextUrl )
            $url = U("/Home/Html/".$nextAction, NULL , "", FALSE , TRUE );
        else
            $url = U('/Admin/Index/index');
        $this->assign("jumpUrl" , $url );
        $this->success("最近更新生成成功!");
    }


}
?>
