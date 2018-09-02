<?php

namespace app\admin\model;

use think\Model;

class SheduleVacation extends Model
{
    // 表名
    protected $name = 'shedule_vacation';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'status_text',
        'from_text',
        'student_text',
        'lesson_text',
        'teacher_text'
    ];
    public function getLessonTextAttr($value,$data)
    {
        return db('lesson')->where('id',$data['lesson_id'])->value('name');
    }

    public function getCreatorAttr($value,$data)
    {
        $value=$value?$value:$data['creator'];
        return model('User')->where('id',$value)->value('username');
    }

    public function getTeacherTextAttr($value,$data)
    {
        return db('teacher')->where('id',$data['teacher_id'])->value('username');
    }

    public function getStudentTextAttr($value,$data)
    {
        return db('student')->where('id',$data['student_id'])->value('username');
    }
    
    public function getStatusList()
    {
        return [0=>'忽略',1=>'待审核',2=>'通过',3=>'已调课'];
    }     

    public function getFromList()
    {
        return ['1' => '家长端',2=>'教务端'];
    }     


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getFromTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['from'];
        $list = $this->getFromList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
