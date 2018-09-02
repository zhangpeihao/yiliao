<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/8 16:34
 */
namespace app\common\model;

use think\Model;

class SheduleComment extends Model{
    protected $name='shedule_comment';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=['creator_text','lesson'];

    protected static function init()
    {
        SheduleComment::event('after_insert',function ($comment){
           Shedule::update(['comment_status'=>1],['id'=>$comment->shedule_id]);
        });
    }

    public function getPicsAttr($value,$data)
    {
        $value=$value?$value:$data['pics'];
        if (strstr($value,',')==false){
            $tmp=explode('https://',$value);
            $tmp=array_filter($tmp);
            foreach ($tmp as &$v){
                $v=request()->scheme(). '://'.$v;
            }
            return array_values($tmp);
        }else{
            return array_filter(explode(',',trim($value)));
        }
    }

    public function getVideoAttr($value,$data)
    {
        $value=$value?$value:$data['video'];
        if (strstr($value,',')==false){
            $tmp=explode('https://',$value);
            $tmp=array_filter($tmp);
            foreach ($tmp as &$v){
                $v=request()->scheme(). '://'.$v;
            }
            return array_values($tmp);
        }else{
            return array_filter(explode(',',trim($value)));
        }
    }

    public function getBeginTimeAttr($value,$data)
    {
        return string_to_time($value);
    }

    public function getEndTimeAttr($value,$data)
    {
        return string_to_time($value);
    }

    public static function is_comment($student_id,$shedule_id)
    {
        $check=self::where('student_id',$student_id)->where('shedule_id',$shedule_id)->where('status',1)->find();
        if ($check){
            return $check;
        }else{
            return (object)[];
        }
    }

    public function getWeekAttr($value,$data)
    {
        $value=$value?$value:$data['week'];
        $list=[0=>'星期日',1=>'星期一',2=>'星期二',3=>'星期三',4=>'星期四',5=>'星期五',6=>'星期六'];
        return $list[$value];
    }

    public function getCreatorTextAttr($value,$data)
    {
        return db('user')->where('id',$data['creator'])->value('username');
    }

    public function getLessonAttr($value,$data)
    {
        return db('lesson')->where('id',$data['lesson_id'])->value('name');
    }


}