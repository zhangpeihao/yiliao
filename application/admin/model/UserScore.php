<?php

namespace app\admin\model;

use think\Model;

class UserScore extends Model
{
    // 表名
    protected $name = 'user_score';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'operate_text',
        'type_text',
        'ctime_text',
        'utime_text',
        'username'
    ];

    public function getUsernameAttr($value,$data)
    {
        return (string)db('user')->where('id',$data['uid'])->value('username');
    }
    
    public function getOperateList()
    {
        return ['1' => '加','-1'=>'减'];
    }     

    public function getTypeList()
    {
        return [1=>'发布练习视频',2=>'商城消费兑换',3=>'商城消费',4=>'拉新赠送',5=>'新用户注册'];
    }     


    public function getOperateTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['operate'];
        $list = $this->getOperateList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['type'];
        $list = $this->getTypeList();
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

    protected function setCtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setUtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
