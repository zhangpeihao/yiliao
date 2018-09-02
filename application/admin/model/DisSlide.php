<?php

namespace app\admin\model;

use think\Model;

class DisSlide extends Model
{
    // 表名
    protected $name = 'dis_slide';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = 'ctime';
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'ctime_text','status_text'
    ];

    public function getStatusTextAttr($value,$data)
    {
        $list=[0=>'禁用',1=>'启用'];
        return $list[$data['status']];
    }

    public function getCtimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['ctime'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setCtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
