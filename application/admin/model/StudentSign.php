<?php

namespace app\admin\model;

use think\Model;

class StudentSign extends Model
{
    // 表名
    protected $name = 'student_sign';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'dec_lesson_text',
        'status_text',
        'student_text',
        'lesson_text',
        'creator_text'
    ];

    public function getLessonTextAttr($value,$data)
    {
        return (string)model('Lesson')->where('id',$data['lesson_id'])->value('name');
    }

    public function getCreatorTextAttr($value,$data)
    {
        return (string)model('user')->where('id',$data['creator'])->value('username');
    }

    public function getClassRoomAttr($value,$data)
    {
        return model('class_room')->where('id',$data['class_room'])->value('name');
    }

    
    public function getDecLessonList()
    {
        return ['1' => '扣课时',0=>'不扣课时'];
    }     

    public function getStatusList()
    {
        return ['0' => '未确认','1'=>'已到达','2'=>'请假','3'=>'迟到','4'=>'早退','5'=>'旷课'];
    }     


    public function getDecLessonTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['dec_lesson'];
        $list = $this->getDecLessonList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStudentTextAttr($value,$data)
    {
        $student=model('Student')->where('id',$data['student_id'])->value('username');
        return $student;
    }

    public function getLessonList()
    {
        if ($res=Lesson::get(['status'=>1])){
            return $res->column('name','id');
        }else{
            return [];
        }
    }



    public function getClassRoomList()
    {
        if ($res=ClassRoom::get(['status'=>1])){
            return $res->column('name','id');
        }else{
            return [];
        }
    }
    public function getStudentList()
    {
        if ($res=Student::get(['status'=>1])){
            return $res->column('username','id');
        }else{
            return [];
        }
    }
}
