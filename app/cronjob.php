<?php
/**
* PHP CLI模式下的定时任务管理
*
* 功能1：进程管理和子线程的管理
* 功能2: 运行定时任务代码
*
* @file		cronjob.php
* @author	ZhangYe <banfg56@gmail.com>
* @version 	0.01
*/

/**
* 判断当前PHP执行的环境
*/
define('ISCLI', PHP_SAPI === 'cli'); 
@set_time_limit(0);
/**
* 后台守护进程类
*/
class Deamon
{
	public $m_pid = NULL;
	public $m_user = NULL;
	private $m_process_active = TRUE;
	private $m_jobs;
	/**
	* 守护进程类的构造函数
	*
	* @access public
	*/
	function Deamon()
	{
		if( ISCLI )
		{
			$this->init();
			$this->run();
		}
		else
		{
			echo "程序执行在非CLI环境中，请从命令行运行";
			exit();
		}
		
	}
	
	/**
	* 函数init,初始化后台守护类
	*
	* @access public
	* 
	* @return void
	*/
	protected function init()
	{
		$this->jobs = array();
		$this->m_pid = getmypid();
		$this->m_user = get_current_user(); 
		$this->m_process_active = TRUE;
	}
	
	public function stop()
	{
		$this->m_process_active = false;
	}
	public function run()
	{
		_worker::L("后台守护进程");
		while ($this->m_process_active ) 
		{
			_worker::L( date('Y-m-d H:i:s'));
			sleep(10);
		}
	}
	
	public function addJob( $worker = NULL )
	{
		//
	}
	
	public function clearJob()
	{
		//
	}
	
	public function status()
	{
		//当前线程执行情况
	}
}

/**
* 线程抽象类
**/
abstract class _worker
{
	abstract protected function run();
	
	/**
	* CURL加载远程页面的内容
	* 
	* @access public 
	*
	* @url		string	 加载的页面URL地址
	* @timeout 	int 	 超时设置
	* @referer	string 	 http请求referer 页面
	* 
	* @return  string	
	*/
	static public function curl_content( $url, $timeout = 10, $referer = "http://www.baidu.com" )
	{
		if(function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,$timeout);
			if($referer){
				curl_setopt ($ch, CURLOPT_REFERER, $referer);
			}
			if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
				curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			}
			$content = curl_exec($ch);
			curl_close($ch);
			if($content){
				return $content;
			}
		}
		$ctx = stream_context_create(array('http'=>array('timeout'=>$timeout)));
		$content = @file_get_contents($url, 0, $ctx);
		if($content){
			return $content;
		}
		return false;
	}
	
	static public function L( $str)
	{
		printf("%s\n", $str);
	}
}

class novelWorker extends _worker
{
	public function run()
	{
		self::L("Novel cron job ");
		//
	}
}

$novelDeamon = new Deamon();
exit(0);
?>