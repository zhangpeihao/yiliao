<?php

namespace app\admin\model;

use think\Model;

class TopicPost extends Model
{
    // 表名
    protected $name = 'topic_post';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'type_text',
        'is_top_text',
        'time_text',
        'status_text',
        'ctime_text',
        'utime_text',
        'username'
    ];


    public function getUsernameAttr($val,$data)
    {
        return db('user')->where('id',$data['uid'])->value('username');
    }
    
    public function getTypeList()
    {
        return [0=>'公开',1=>'仅限老师'];
    }     

    public function getIsTopList()
    {
        return ['1' => '是',0=>'否'];
    }     

    public function getStatusList()
    {
        return ['1' => '正常',0=>'禁用'];
    }     


    public function getTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['type'];
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsTopTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_top'];
        $list = $this->getIsTopList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
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

    protected function setTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
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
