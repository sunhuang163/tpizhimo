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

namespace traits\model;

trait View
{

    /**
     * 自动检测数据表信息
     * @access protected
     * @return void
     */
    protected function _checkTableInfo()
    {}

    /**
     * 得到完整的数据表名
     * @access public
     * @return string
     */
    public function getTableName()
    {
        if (empty($this->trueTableName)) {
            $tableName = '';
            $len = 0;
            foreach ($this->viewFields as $name => $view) {
                // 获取数据表名称
                if (isset($view['_table'])) {
                    // 2011/10/17 添加实际表名定义支持 可以实现同一个表的视图
                    $tableName .= $view['_table'];
                    $prefix    = $this->tablePrefix;
                    $tableName = preg_replace_callback("/__([A-Z_-]+)__/sU", function ($match) use ($prefix) {return $prefix . strtolower($match[1]);}, $tableName);
                } else {
                    $tableName .= \think\Loader::table($name)->getTableName();
                }
                // 表别名定义
                $tableName .= !empty($view['_as']) ? ' ' . $view['_as'] : ' ' . $name;
                // 支持ON 条件定义
                $tableName .= !empty($view['_on']) ? ' ON ' . $view['_on'] : '';
                if (!empty($view['_type'])) {
                    // 指定JOIN类型 例如 RIGHT INNER LEFT 下一个表有效
                    $type = strtoupper($view['_type']);
                    $tableName .= ' ' . $type . ' JOIN ';
                    if ($view == end($this->viewFields)) {
                        $len = strlen($type) + 7;
                    }
                }
            }
            $this->trueTableName = $len ? substr($tableName, 0, -$len) : $tableName;
        }

        return $this->trueTableName;
    }

    /**
     * 表达式过滤方法
     * @access protected
     * @param string $options 表达式
     * @return void
     */
    protected function _options_filter(&$options)
    {
        $viewFields = [];
        foreach ($this->viewFields as $name => $val) {
            $k   = isset($val['_as']) ? $val['_as'] : $name;
            $val = $this->_checkFields($name, $val);
            foreach ($val as $key => $field) {
                if (is_numeric($key)) {
                    $viewFields[$field] = $k . '.' . $field;
                } elseif ('_' != substr($key, 0, 1)) {
                    // 以_开头的为特殊定义
                    if (false !== strpos($key, '*') || false !== strpos($key, '(') || false !== strpos($key, '.')) {
                        //如果包含* 或者 使用了sql方法 则不再添加前面的表名
                        $viewFields[$field] = $key;
                    } else {
                        $viewFields[$field] = $k . '.' . $key;
                    }
                }
            }
        }
        if (empty($options['field'])) {
            $options['field'] = '*';
        }
        $options['field'] = $this->checkFields($options['field'], $viewFields);
        if (isset($options['group'])) {
            $options['group'] = $this->checkGroup($options['group'], $viewFields);
        }
        if (isset($options['where'])) {
            $options['where'] = $this->checkCondition($options['where'], $viewFields);
        }
        if (isset($options['order'])) {
            $options['order'] = $this->checkOrder($options['order'], $viewFields);
        }
    }

    /**
     * 检查是否定义了所有字段
     * @access protected
     * @param string $name  模型名称
     * @param array $fields 字段数组
     * @return array
     */
    private function _checkFields($name, $fields)
    {
        if (false !== $pos = array_search('*', $fields)) {
            // 定义所有字段
            $fields = array_merge($fields, \think\Loader::table($name)->getFields());
            unset($fields[$pos]);
        }

        return $fields;
    }

    /**
     * 检查条件中的视图字段
     * @access protected
     * @param mixed $data   条件表达式
     * @param array $fields 视图字段数组
     * @return array
     */
    protected function checkCondition($where, $viewFields)
    {
        if (is_array($where)) {
            $_where = [];
            foreach ($where as $field => $value) {
                if ('_complex' != $field) {
                    $value = [$field => $value];
                    $parent = 1;
                } else {
                    $parent = 0;
                }
                foreach ($value as $key => $val) {
                    if (0 !== strpos($key, '_')) {
                        if ($items = preg_split('/[\|\&]/', $key, -1, PREG_SPLIT_OFFSET_CAPTURE)) {
                            // 倒序找出的字段数组，以便从后向前替换
                            $items = array_reverse($items);
                            foreach ($items as $item) {
                                if (isset($viewFields[$item[0]])) { // 存在视图字段
                                    $key = substr_replace($key, $viewFields[$item[0]], $item[1], strlen($item[0]));
                                }
                            }
                        }
                        if (isset($val[0]) && 'exp' == $val[0] && !empty($val[1])) {
                            if (preg_match_all('/(?<![\'\.])\b(\w+)\b(?![\'])/', $val[1], $matches, PREG_OFFSET_CAPTURE)) {
                                // 倒序找出的字段数组，以便从后向前替换
                                $items = array_reverse($matches[0]);
                                foreach ($items as $item) {
                                    if (isset($viewFields[$item[0]])) {
                                        $val[1] = substr_replace($val[1], $viewFields[$item[0]], $item[1], strlen($item[0]));
                                    }
                                }
                            }
                        }
                    } elseif (is_string($val) && ('__query' == $key || '_string' == $key)) {
                        if (preg_match_all('/(?<![=\'\.])\b(\w+)\b(?![&\'])/', $val, $matches, PREG_OFFSET_CAPTURE)) {
                            // 倒序找出的字段数组，以便从后向前替换
                            $items = array_reverse($matches[0]);
                            foreach ($items as $item) {
                                if (isset($viewFields[$item[0]])) {
                                    $val = substr_replace($val, $viewFields[$item[0]], $item[1], strlen($item[0]));
                                }
                            }
                        }
                    }
                    if ($parent) {
                        $_where[$key] = $val;
                    } else {
                        $_where[$field][$key] = $val;
                    }
                }
            }
            return $_where;
        } else {
            if (preg_match_all('/(?<![=\'\.])\b(\w+)\b(?![&\'])/', $where, $matches, PREG_OFFSET_CAPTURE)) {
                // 倒序找出的字段数组，以便从后向前替换
                $items = array_reverse($matches[0]);
                foreach ($items as $item) {
                    if (isset($viewFields[$item[0]])) {
                        $where = substr_replace($where, $viewFields[$item[0]], $item[1], strlen($item[0]));
                    }
                }
            }
            return $where;
        }
    }

    /**
     * 检查Order表达式中的视图字段
     * @access protected
     * @param string|array $order 字段
     * @param array $fields       视图字段数组
     * @return string
     */
    protected function checkOrder($order = '', $viewFields)
    {
        if (empty($order)) {
            return '';
        } elseif (is_string($order)) {
            $order = explode(',', $order);
        }
        $_order = [];
        foreach ($order as $key => $field) {
            if (is_numeric($key)) {
                if ($pos = strpos($field, ' ')) {
                    $sort  = substr($field, $pos);
                    $field = substr($field, 0, $pos);
                } else {
                    $sort = '';
                }
            } else {
                $sort = ' ' . $field;
                $field = $key;
            }
            if (isset($viewFields[$field])) {
                // 存在视图字段
                $field = $viewFields[$field] . $sort;
            }
            $_order[] = $field;
        }

        return $_order;
    }

    /**
     * 检查Group表达式中的视图字段
     * @access protected
     * @param string $group 字段
     * @param array $fields 视图字段数组
     * @return string
     */
    protected function checkGroup($group = '', $viewFields)
    {
        if (empty($group)) {
            return '';
        } elseif (is_string($group)) {
            $group = explode(',', $group);
        }
        foreach ($group as &$field) {
            if (isset($viewFields[$field])) {
                // 存在视图字段
                $field = $viewFields[$field];
            }
        }

        return implode(',', $group);
    }

    /**
     * 检查fields表达式中的视图字段
     * @access protected
     * @param string $fields 字段
     * @param array $fields  视图字段数组
     * @return string
     */
    protected function checkFields($fields = '*', $viewFields)
    {
        if (is_string($fields)) {
            if ('*' == $fields) {
                return $viewFields;
            } else {
                $fields = explode(',', $fields);
            }
        }
        $_fields = [];
        foreach ($fields as $key => $field) {
            if (is_numeric($key)) {
                if ($pos = strpos($field, ' ')) {
                    $alias = substr($field, $pos);
                    $field = substr($field, 0, $pos);
                } else {
                    $alias = ' AS ' . $field;
                }
            } else {
                $alias = ' AS ' . $field;
                $field = $key;
            }
            if (strpos($field, '(')) {
                if (preg_match_all('/(?<!\s)\b(\w+)\b(?![\.\(])/', $field, $matches, PREG_OFFSET_CAPTURE)) {
                    // 倒序找出的字段数组，以便从后向前替换
                    $items = array_reverse($matches[0]);
                    foreach ($items as $item) {
                        if (isset($viewFields[$item[0]])) {
                            $field = substr_replace($field, $viewFields[$item[0]], $item[1], strlen($item[0]));
                        }
                    }
                }
            } elseif (isset($viewFields[$field])) {
                // 存在视图字段
                $field = $viewFields[$field];
            }
            $_fields[] = $field . $alias;
        }

        return $_fields;
    }
}
