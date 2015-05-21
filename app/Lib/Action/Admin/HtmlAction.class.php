<?php
/***
* @Author  banfg56
* @Date    2015-05-21
* @Info    将网站内容生成静态html
* @File    HtmlAction.class.php
*
*/
class HtmlAction extends BackAction 
{
	public function index()
    {
	   $this->display();
	}

    //创建首页静态页面
    public function createHome()
    {
        // code here
    }
    
    //创建最新更新，排行，完结等
    public function createFinish()
    {
        // code here
    }

    //创建列表页首页静态页面
    public function createCate()
    {
        // code here
    }

    //创建分类列表页
    public function createCatelist()
    {
        // code here
    }

    //创建某个内容静态页面
    public function createNovel()
    {
        // code here 
    }

    //创建最近更新的静态页面
    public function createLast()
    {
        //code here
    }

    //百度 sitemap
    public function baiduSitemap()
    {
        //code here
    }

    //谷歌  sitemap
    public function googleSitemap()
    {
        //code here
    }


}
?>