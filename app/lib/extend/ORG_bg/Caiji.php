<?php
/**
 * 小说采集库函数重写
 * Caiji.php
 *
 * @author banfg56 <banfg56@gmail.com>
 * @date   06/07/2016
 */

namespace org;

class Caiji
{
    /**
     * 采集驱动对象
     * @var resource
     */
    private static $caiji;

    //获取所有驱动
    public static function drivers()
    {
        $driverdata = [];
        return $driverdata;
    }
    //
    public static function init($type = 'day66', $url = null)
    {
        /*实例化对象 */
        $class    = '\\org\\Caiji\\driver\\' . ucwords($type);
        self::$caiji = new $class($imgname);
        return self::$caiji;
    }

    public  function __call($method, $params)
    {
        self::$caiji || self::init();
        return call_user_func_array([self::$caiji, $method], $params);
    }
}
