<?php

namespace app\admin\model;

use think\Model;

class SheduleComment extends Model
{
    // 表名
    protected $name = 'shedule_comment';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'week_text',
        'status_text',
        'banji_text',
        'lesson_text',
        'student_name'
    ];

    public function getStudentNameAttr($value,$data)
    {
        return (string)db('student')->where('id',$this->data['student_id'])->value('username');
    }


    public function getBanjiTextAttr($value,$data)
    {
        return db('banji')->where('id',$data['banji_id'])->value('name');
    }


    public function getLessonTextAttr($value,$data)
    {
        return db('lesson')->where('id',$data['lesson_id'])->value('name');
    }
    
    public function getWeekList()
    {
        return [0=>'星期日',1=>'星期一',2=>'星期二',3=>'星期三',4=>'星期四',5=>'星期五',6=>'星期六'];
    }     

    public function getStatusList()
    {
        return ['1' =>'正常',0=>'禁用'];
    }

    public function getStatusTextAttr($value,$data)
    {
        return $this->getStatusList()[$data['status']];
    }

    public function getWeekTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['week'];
        $list = $this->getWeekList();
        return isset($list[$value]) ? $list[$value] : '';
    }



    public function getBeginTimeAttr($value,$data)
    {
        $value=$value?$value:$data['begin_time'];
        return string_to_time($value);
    }

    public function getEndTimeAttr($value,$data)
    {
        $value=$value?$value:$data['end_time'];
        return string_to_time($value);
    }
    public function setBeginTimeAttr($value,$data)
    {
        $value=$value?$value:$data['begin_time'];
        return format_string_time($value);
    }

    public function setEndTimeAttr($value,$data)
    {
        $value=$value?$value:$data['end_time'];
        return format_string_time($value);
    }


}
