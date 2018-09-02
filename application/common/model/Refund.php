<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/12 16:33
 */
namespace app\common\model;

use think\Model;

class Refund extends Model{
    protected $name='refund';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    public static function init()
    {
        Refund::event('after_insert',function($refund){
            $contract=Contract::get(['sno'=>$refund['sno']]);
            $info=ContractRecord::create([
                'type'=>4,'sno'=>$refund['sno'],'student_id'=>$contract['student_id'],'startdate'=>$contract['startdate'],
                'enddate'=>$contract['enddate'],'lesson_id'=>$contract['lesson_id'],'lesson_count'=>$refund['lesson_count']*-1,
                'total_fee'=>$refund['money']*-1,'remark'=>'退费','creator'=>$refund['creator']
            ],true);
            if ($info){
                Finance::create(['refund_money'=>$refund['money'],'date'=>date('Y-m-d',time())],true);
            }
        });
    }
    
    public static function makeSno()
    {
        $count=Refund::whereTime('createtime','today')->count();
        if ($count<1000){
            if ($count>100){
                $count='0'.$count;
            }elseif ($count>10){
                $count='00'.$count;
            }elseif ($count>0){
                $count='000'.$count;
            }elseif($count==0){
                $count='0001';
            }
        }
        return 'TF'.date('Ymd').$count;
    }

}