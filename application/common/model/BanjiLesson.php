<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/8 14:16
 */
namespace app\common\model;

use think\Model;

class BanjiLesson extends Model{
    protected $name='banji_lesson';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=[
        'lesson','teacher','enddate','status_text','frequency_text','week','class_room_id','banji_info','next_lesson'
    ];

    public function getNextLessonAttr($value,$data)
    {
        $res=model('shedule')->where('banji_lesson_id',$data['id'])->order('date desc')->find();
        if ($res){
            return $res->toArray();
        }else{
            return [];
        }
    }

/*    public static function init()
    {
        //检测一对一班级的录入，生成课表
        BanjiLesson::event('after_insert',function ($banji_lesson){
            $banji_lesson['banji_lesson_id']=$banji_lesson['id'];
            //根据该班课学员来生成课程
            $banji_student=db('banji_student')->where(['banji_id'=>$banji_lesson['banji_id'],'status'=>1])
                            ->where('type',1)->column('student_id');
            $res=Shedule::auto_shedule($banji_lesson->data,$banji_lesson['creator'],$banji_student);
        });
    }*/

    public function getLessonAttr($value,$data)
    {
        $value=$value?$value:$data['lesson_id'];
        return (string)model('Lesson')->where('id',$value)->value('name');
    }

    public function getClassRoomIdAttr($value,$data)
    {
        return $data['class_room'];
    }

    public function getClassRoomAttr($value,$data)
    {
        return (string)db('ClassRoom')->where('id',$value)->value('name');
    }

    public function getTeacherAttr($value,$data)
    {
        $value=$value?$value:$data['teacher_id'];
        return (string)db('Teacher')->where('id',$value)->value('username');
    }

    public static function getLessonList($banji_id)
    {
        return self::all(['banji_id'=>$banji_id,'status'=>1]);
    }


    public function getEnddateAttr($value,$data)
    {
        return (string)db('shedule')->where(['banji_id'=>$data['banji_id'],'banji_lesson_id'=>$data['id']])->order('date desc')->value('date');
    }

    public function getStatusTextAttr($value,$data)
    {
        $value=$value?$value:$data['status'];
        $list=[0=>'已删除',1=>'未接课',2=>'已结课'];
        return $list[$value];
    }

    public function getFrequencyTextAttr($value,$data)
    {
        $value=$value?$value:$data['frequency'];
        $list=[0=>'无',1=>'每天',2=>'隔天',3=>'每周',4=>'隔周',5=>'自定义'];
        return $list[$value];
    }

    public function getWeekAttr($value,$data)
    {
        $value=date('w',strtotime($data['startdate']));
        $list=[0=>'星期日',1=>'星期一',2=>'星期二',3=>'星期三',4=>'星期四',5=>'星期五',6=>'星期六'];
        return $list[$value];
    }

    public function getBanjiInfoAttr($value,$data)
    {
        return (array)db('banji')->where('id',$data['banji_id'])->find();
    }

    public static function copy_banji_lesson($student_id,$banji_id,$banji_lesson_id,$agency_id=0)
    {
        if ($banji_lesson_id){
                return BanjiStudent::create([
                    'agency_id'=>$agency_id,
                    'banji_id'=>$banji_id,
                    'student_id'=>$student_id,
                    'banji_lesson_id'=>$banji_lesson_id,
                    'status'=>1
                ]);
        }else{
            return false;
        }
    }
}