<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\model;

use think\Model;

class Merge extends Model
{

    protected $models    = []; //模型列表
    protected $master    = ''; //  主模型
    protected $joinType  = 'INNER'; //  聚合模型的查询JOIN类型
    protected $fk        = ''; //  外键名 默认为主表名_id
    protected $mapFields = array(); //  需要处理的模型映射字段，避免混淆 array( id => 'user.id'  )
    /**
     * 架构函数
     * @access public
     * @param array|object $data 数据
     */
    public function __construct($data = [])
    {
        parent::__construct($data);
        // 聚合模型的字段信息
        if (empty($this->field) && !empty($this->models)) {
            $fields = array();
            foreach ($this->models as $model) {
                // 获取模型的字段信息
                $result  = self::db()->name($model)->getTableInfo('', 'fields');
                $_fields = array_keys($result);
                $fields  = array_merge($fields, $_fields);
            }
            $this->field = $fields;
        }
        // 设置第一个模型为主表模型
        if (empty($this->master) && !empty($this->models)) {
            $this->master = $this->models[0];
        }
        // 主表的主键名
        $this->pk = self::db()->name($master)->getTableInfo('', 'pk');

        // 设置默认外键名 仅支持单一外键
        if (empty($this->fk)) {
            $this->fk = strtolower($this->master) . '_id';
        }

        foreach ($this->models as $model) {
            $tableName[] = self::db()->name($model)->getTable() . ' ' . $model;
        }
        self::$table = implode(',', $tableName);

    }

}
