<?php

namespace app\admin\model;

use think\Model;

class Lesson extends Model
{
    // 表名
    protected $name = 'lesson';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'status_text','category'
    ];


    public function getCategoryAttr($value,$data)
    {
        return db('mall_category')->where('id',$data['cid'])->value('name');
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


    public function getCreatorAttr($value,$data)
    {
        return User::where('id',$value)->value('username');
    }

}
