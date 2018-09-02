<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/10 15:03
 */
namespace app\common\model;

use think\Model;

class SheduleFinish extends Model{
    protected $name='shedule_finish';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=[
        'teacher_name','lesson','banji_name'
    ];

    public function getLessonAttr($value,$data)
    {
        return (string)model('Lesson')->where('id',$data['lesson_id'])->value('name');
    }

    public function getBanjiNameAttr($value,$data)
    {
        return (string)model('Banji')->where('id',$data['banji_id'])->value('name');
    }

    public function getWeekAttr($value,$data)
    {
        $value=$value?$value:$data['week'];
        $list=[0=>'星期日',1=>'星期一',2=>'星期二',3=>'星期三',4=>'星期四',5=>'星期五',6=>'星期六'];
        return $list[$value];
    }

    public function getTeacherNameAttr($value,$data)
    {
        $value=$value?$value:$data['teacher_id'];
        return (string)model('Teacher')->where('id',$value)->value('username');
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

    public function getClassRoomAttr($value,$data)
    {
        $value=$value?$value:$data['class_room'];
        return (string)model('ClassRoom')->where('id',$value)->value('name');
    }


    public function getDecNumAttr($value,$data)
    {
        return floatval($value);
    }

    public function getCreatorAttr($value,$data)
    {
        $value=$value?$value:$data['creator'];
        return model('User')->where('id',$value)->value('username');
    }

}