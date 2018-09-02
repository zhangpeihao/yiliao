<?php

namespace app\admin\model;

use function GuzzleHttp\Psr7\str;
use think\Model;

class Banji extends Model
{
    // 表名
    protected $name = 'banji';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'type_text',
        'status_text',
        'lesson',
        'header_name'
    ];
    

    
    public function getTypeList()
    {
        return ['1' => '建班课',2=>'一对一'];
    }     

    public function getStatusList()
    {
        return ['1' => '正常',0=>'禁用',2=>'已完结'];
    }     


    public function getTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['type'];
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getHeaderNameAttr($value,$data)
    {
        return (string)db('teacher')->where('id',$data['header_uid'])->value('username');
    }

    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getMemberCountAttr($value,$data)
    {
        return model('BanjiStudent')->where('student_id',$data['id'])->where('status',1)->count();
    }

    public function getLessonAttr($value,$data)
    {
        $value=$value?$value:$data['lesson_id'];
        return (string)model('Lesson')->where('id',$value)->value('name');
    }

    public function getStudentAttr($value,$data)
    {
        $value=$value?$value:$data['student_id'];
        return (string)model('Student')->where('id',$value)->value('username');
    }

    public function getClassRoomAttr($value,$data)
    {
        return (string)model('ClassRoom')->where('id',$value)->value('name');
    }

    public function getTeacherAttr($value,$data)
    {
        $value=$value?$value:$data['teacher_id'];
        return (string)model('Teacher')->where('id',$value)->value('username');
    }


}
