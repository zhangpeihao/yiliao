<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/9 10:51
 */
namespace app\common\model;

use think\Model;

class SheduleVacation extends Model{
    protected $name='shedule_vacation';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=[
        'status_text','student','lesson','mobile','shedule_info','creator_text','updator_text'
    ];

    protected $student_info;

    public function getStudentAttr($value,$data)
    {
        $value=$value?$value:$data['student_id'];
        $student_info=Student::get($value);
        $this->student_info=$student_info;
        return $student_info['username'];
    }

    public function getMobileAttr($value,$data)
    {
        return $this->student_info['mobile'];
    }

    public function getLessonAttr($value,$data)
    {
        $value=$value?$value:$data['lesson_id'];
        return model('Lesson')->where('id',$value)->value('name');
    }

    public function getStatusTextAttr($value,$data)
    {
        $value=$value?$value:$data['status'];
        $list=[0=>'忽略',1=>'待审核',2=>'通过',3=>'已调课'];
        return $list[$value];
    }

    public function getFromAttr($value,$data)
    {
        $value=$value?$value:$data['from'];
        $list=[0=>'未知',1=>'家长端',2=>'教师'];
        return $list[$value];
    }

    public function getCreateTimeAttr($value,$data)
    {
        $value=$value?$value:$data['createtime'];
        return $this->formatDateTime($value,'Y-m-d H:i');
    }

    public function getUpdateTimeAttr($value,$data)
    {
        $value=$value?$value:$data['updatetime'];
        return $this->formatDateTime($value,'Y-m-d H:i');
    }

    public function getSheduleInfoAttr($value,$data)
    {
        return Shedule::get($data['shedule_id']);
    }

    public function getCreatorTextAttr($value,$data)
    {
        return (string)db('user')->where('id',$data['creator'])->value('username');
    }

    public function getUpdatorTextAttr($value,$data)
    {
        if ($data['updator']){
            return (string)db('user')->where('id',$data['updator'])->value('username');
        }else{
            return "";
        }
    }
}