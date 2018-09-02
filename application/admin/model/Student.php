<?php

namespace app\admin\model;

use think\Model;

class Student extends Model
{
    // 表名
    protected $name = 'student';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'gender_text',
        'learn_status_text',
        'status_text',
        'agency_text'
    ];


    public function getAgencyTextAttr($value,$data)
    {
        return (string)db('agency')->where('id',$data['agency_id'])->value('name');
    }

    public function getGenderList()
    {
        return ['1' =>'男','2'=>'女','0'=>'未知'];
    }

    public function getLearnStatusList()
    {
        return ['1' =>'在读','2'=>'试听',3=>'过期'];
    }

    public function getStatusList()
    {
        return ['0'=>'禁用','1' => '未签约',2=>'未排课'];
    }



    public function getGenderTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['gender'];
        $list = $this->getGenderList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getLearnStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['learn_status'];
        $list = $this->getLearnStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
}
