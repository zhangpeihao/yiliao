<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/8 13:10
 */
namespace app\common\model;

use app\common\library\Auth;
use think\Model;

class StudentSign extends Model{
    protected $name='student_sign';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=['status_text','creator_text'];

    protected static function init()
    {
        StudentSign::event('before_insert',function ($student_sign){
            $auth=Auth::instance();
            $student_sign->agency_id=$auth->agency_id;
        });
    }

    public static function is_sign($student_id,$shedule_id)
    {
        $check=self::where(['student_id'=>$student_id,'shedule_id'=>$shedule_id])->find();
        if ($check){
            return $check;
        }else{
            return (object)[];
        }
    }

    public function getStatusTextAttr($value,$data)
    {
        if (empty($data['status'])){$data['status']=0;}
        $status=['0' => '未确认','1'=>'已到达','2'=>'请假','3'=>'迟到','4'=>'早退','5'=>'旷课'];
        return $status[$data['status']];
    }

    public function getCreatorTextAttr($value,$data)
    {
        if (empty($data['id'])){return "";}
        return (string)model('User')->where('id',$data['id'])->value('username');
    }

    public function getCreatetimeAttr($value,$data)
    {
        if (empty($value)){return "";}
        return formatTime($value);
    }
    public function getUpdatetimeAttr($value,$data)
    {
        if (empty($value)){return "";}
        return formatTime($value);
    }
}