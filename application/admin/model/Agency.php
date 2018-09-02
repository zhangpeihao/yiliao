<?php

namespace app\admin\model;

use think\Model;

class Agency extends Model
{
    // 表名
    protected $name = 'agency';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'status_text','creator_text','updator_text'
    ];


    public function getCreatorTextAttr($value,$data)
    {
        return (string)db('user')->where('id',$data['creator'])->value('username');
    }

    public function getUpdatorTextAttr($value,$data)
    {
        return (string)db('user')->where('id',$data['updator'])->value('username');
    }
    
    public function getStatusList()
    {
        return ['1' => '启用',0=>'禁用'];
    }     


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
