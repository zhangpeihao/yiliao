<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/9 11:20
 */
namespace app\common\model;

use think\Model;

class SheduleDispatch extends Model{
    protected $name='shedule_dispatch';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=['teacher','creator','status_text'];

    public static function init()
    {
        SheduleDispatch::event('after_insert',function($dispatch){
           if ($dispatch['shedule_id']){
               //0=>'禁用',1=>'未结课',2=>'已结课',3=>'已调课'
               Shedule::update(['status'=>3,'updator'=>$dispatch['updator']],['id'=>$dispatch['shedule_id']]);
               SheduleVacation::update(['status'=>3,'updator'=>$dispatch['updator']],['shedule_id'=>$dispatch['shedule_id']]);
           }
        });
    }

    public function getTeacherAttr($value,$data)
    {
        $value=$value?$value:$data['teacher_id'];
        return model('Teacher')->where('id',$value)->value('username');
    }

    public function getCreatorAttr($value,$data)
    {
        $value=$value?$value:$data['creator'];
        return model('User')->where('id',$value)->value('username');
    }

    public function getBeginTimeAttr($value,$data)
    {
        return string_to_time($value);
    }

    public function getEndTimeAttr($value,$data)
    {
        return string_to_time($value);
    }

    public function setWeekAttr($value,$data)
    {
        $value=$value?$value:$data['date'];
        return date('w',strtotime($value));
    }

    public function getStatusTextAttr($value,$data)
    {
        $value=$value?$value:$data['status'];
        $list=[0=>'忽略',1=>'待审核',2=>'已同意',3=>'已调课'];
        return $list[$value];
    }
}