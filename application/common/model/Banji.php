<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/3 16:51
 */
namespace app\common\model;

use think\Model;

class Banji extends Model{
    protected $name='banji';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $dateFormat='Y-m-d H:i';

    protected $nextLesson;
    protected $nextLessonTime;

    protected $append=[
        'lesson','next_lesson','member_count','rest_lesson_count',
        'banji_lesson','history_count','header_user','status_text','student_list',
        'is_full'
    ];

    public static function init()
    {
        //检测一对一班级的录入，生成课表
        Banji::event('after_insert',function ($banji){
            if ($banji['type']==2){
                $banji['banji_id']=$banji['id'];
                //创建班课
                $info=BanjiLesson::create($banji->data,true);
                if ($info){
                    model('Student')->where('id',$banji['student_id'])->setInc('rest_lesson',$banji['lesson_count']);
                }
            }
        });
    }

    public function getNextLessonAttr($value,$data)
    {
        $res=model('Shedule')->where('banji_id',$data['id'])->whereTime('date','>',time())
                    ->order('date asc,begin_time asc')->find();
        if(empty($res)){
            return $res=(object)[];
        }else{
            return $res->toArray();
        }
    }

    public function getLessonAttr($value,$data)
    {
        $value=$value?$value:$data['lesson_id'];
        return (string)model('Lesson')->where('id',$value)->value('name');
    }

    public function getMemberCountAttr($value,$data)
    {
        $check=model('shedule')->where('banji_id',$data['id'])->group('student_id')->where('status',1)->count();
        return $check;
    }

    public function getRestLessonCountAttr($value,$data)
    {
        $num=model('Shedule')->group('date,begin_time')->where(['banji_id'=>$data['id'],'status'=>1])->count();
        return $num;
    }

    public function getBanjiLessonAttr($value,$data)
    {
        return BanjiLesson::getLessonList($data['id']);
    }

    public function getHistoryCountAttr($value,$data)
    {
        return model('Shedule')->group('date,begin_time')->where(['banji_id'=>$data['id'],'status'=>2])->count();
    }

    public function getHeaderUserAttr($value,$data)
    {
        if ($data['header_uid']){
            return (string)model('Teacher')->where('id',$data['header_uid'])->value('username');
        }else{
            return "";
        }
    }

    public function getStatusTextAttr($value,$data)
    {
        if (empty($data['status'])){$data['status']=0;}
        $value=$value?$value:$data['status'];
        $list=[0=>'已删除',1=>'未完结',2=>'已完结'];
        return $list[$value];
    }

    public function getStudentListAttr($value,$data)
    {
        $value=$value?$value:$data['id'];
//        return BanjiStudent::all(['banji_id'=>$value,'status'=>1]);
        $check=db('shedule')->where('banji_id',$data['id'])->where('status',1)->distinct(true)->column('student_id');
        $res=\model('student')->where('id','in',$check)->select();
        if ($res){
            return $res;
        }else{
            return [];
        }
    }

    public function getIsFullAttr($value,$data)
    {
        if ($data['id']){
            return BanjiStudent::is_full($data['id']);
        }else{
            return 0;
        }
    }
}