<?php

namespace app\admin\model;

use think\Model;

class BanjiStudent extends Model
{
    // 表名
    protected $name = 'banji_student';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'status_text','banji_text','student_text','banji_lesson'
    ];
    

    
    public function getStatusList()
    {
        return ['1' =>'正常',0=>'禁用'];
    }

    public function getBanjiLessonAttr($value,$data)
    {
        return \app\common\model\BanjiLesson::get($data['banji_lesson_id']);
    }

    public function getBanjiTextAttr($value,$data)
    {
        return db('banji')->where('id',$data['banji_id'])->value('name');
    }

    public function getStudentTextAttr($value,$data)
    {
        return (string)db('student')->where('id',$data['student_id'])->value('username');
    }

    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
