<?php

namespace app\admin\model;

use think\Model;

class BanjiLesson extends Model
{
    // 表名
    protected $name = 'banji_lesson';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'begin_time_text',
        'end_time_text',
        'frequency_text',
        'status_text',
        'lesson_text',
        'banji_text',
        'teacher_text',
        'summary_text'
    ];

    public function getSummaryTextAttr($value,$data)
    {
        return $this->banji_text.':'.$this->lesson_text.' '.$this->startdate.' '.$this->begin_time.'~'.$this->end_time.'/ '.$this->lesson_count.'课时';
    }

    public function getLessonTextAttr($value,$data)
    {
        return (string)db('lesson')->where('id',$data['lesson_id'])->value('name');
    }

    public function getTeacherTextAttr($value,$data)
    {
        return db('Teacher')->where('id',$data['teacher_id'])->value('username');
    }
    
    public function getFrequencyList()
    {
        return [0=>'无',1=>'每天',2=>'隔天',3=>'每周',4=>'隔周',5=>'自定义'];
    }     

    public function getStatusList()
    {
        return [0=>'已删除',1=>'未结课',2=>'已结课'];
    }     


    public function getBeginTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['begin_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getEndTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['end_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getFrequencyTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['frequency'];
        $list = $this->getFrequencyList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setBeginTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setEndTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


    public function getCreatorAttr($value,$data)
    {
        return db('User')->where('id',$value)->value('username');
    }
    public function getUpdatorAttr($value,$data)
    {
        return db('User')->where('id',$value)->value('username');
    }

    public function getBanjiTextAttr($value,$data)
    {
        return db('banji')->where('id',$data['banji_id'])->value('name');
    }

    public function getClassRoomAttr($value,$data)
    {
        return db('class_room')->where('id',$data['class_room'])->value('name');
    }

}
