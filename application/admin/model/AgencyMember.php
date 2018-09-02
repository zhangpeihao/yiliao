<?php

namespace app\admin\model;

use think\Model;

class AgencyMember extends Model
{
    // 表名
    protected $name = 'agency_member';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'type_text',
        'status_text','agency_name','username','teacher_name'
    ];
    

    
    public function getTypeList()
    {
        return [1=>'机构所有者',2=>'教务',3=>'老师'];
    }     

    public function getStatusList()
    {
        return ['1' =>'正常',0=>'禁用'];
    }     


    public function getTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['type'];
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getAgencyNameAttr($val,$data)
    {
        return db('agency')->where('id',$data['agency_id'])->value('name');
    }

    public function getUsernameAttr($val,$data)
    {
        return db('user')->where('id',$data['uid'])->value('username');
    }

    public function getTeacherNameAttr($val,$data)
    {
        return db('teacher')->where('id',$data['teacher_id'])->value('username');
    }
}
