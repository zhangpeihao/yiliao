<?php

namespace app\admin\model;

use think\Model;

class DisPostComment extends Model
{
    // 表名
    protected $name = 'dis_post_comment';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    
    // 追加属性
    protected $append = [
        'ctime_text',
        'utime_text',
        'status_text',
        'username'
    ];

    public function getUsernameAttr($value,$data)
    {
        if (empty($data['uid'])){return "匿名";}
        return (string)db('user')->where('id',$data['uid'])->value('username');
    }

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


    public function getUtimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['utime'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setCtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setUtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
