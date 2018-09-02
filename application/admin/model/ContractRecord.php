<?php

namespace app\admin\model;

use think\Model;

class ContractRecord extends Model
{
    // 表名
    protected $name = 'contract_record';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'is_old_text',
        'type_text',
        'status_text',
        'lesson_text',
        'student_text',
        'creator_text'
    ];

    public function getCreatorTextAttr($value,$data)
    {
        return (string)db('user')->where('id',$data['creator'])->value('username');
    }

    public function getStudentTextAttr($value,$data)
    {
        return (string)db('student')->where('id',$data['student_id'])->value('username');
    }

    public function getLessonTextAttr($value,$data)
    {
        return (string)db('lesson')->where('id',$data['lesson_id'])->value('name');
    }
    
    public function getIsOldList()
    {
        return ['1' => '是',0=>'否'];
    }     

    public function getTypeList()
    {
        return [0=>'默认',1=>'新签',2=>'赠送',3=>'续费',4=>'退款'];
    }     

    public function getStatusList()
    {
        return ['1' =>'正常',0=>'禁用'];
    }     


    public function getIsOldTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_old'];
        $list = $this->getIsOldList();
        return isset($list[$value]) ? $list[$value] : '';
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




}
