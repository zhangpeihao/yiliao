<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 16:23
 */
namespace app\common\model;

use app\common\library\Auth;
use fast\Random;
use think\Model;

class Contract extends Model{
    protected $name='contract';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=[
        'student_text','lesson_text','creator_text','rest_day','status_text'
    ];

    protected static function init()
    {
        Contract::event('before_insert',function ($contract){
           $auth=Auth::instance();
           $contract->agency_id=$auth->agency_id;
        });
        Contract::event('after_insert',function ($contract){
            $contract_student=[
                'sno'=>$contract['sno'],
                'student_id'=>$contract['student_id'],
                'status'=>1
            ];
            ContractStudent::create($contract_student);
            //做contract_record记录
            $buy_data=$contract->data;
            unset($buy_data['id']);
            if (empty($buy_data['type'])){
                $buy_data['type']=1;
            }
            $buy_data['status']=1;
            ContractRecord::create($buy_data,true);
            if ($contract['give_lesson']>0){
                $give_data=$contract->data;
                unset($give_data['id']);
                $give_data['type']=2;
                $give_data['lesson_count']=$contract['give_lesson'];
                $give_data['price']=0;
                $give_data['other_price']=0;
                $give_data['lesson_money']=0;
                $give_data['total_fee']=0;
                $give_data['status']=1;
                ContractRecord::create($give_data,true);
            }
            if ($contract['type']==1){
                Finance::create(['contract_money'=>$contract['total_fee'],'date'=>date('Y-m-d',time())],true);
            }elseif ($contract['type']==3){
                Finance::create(['continue_money'=>$contract['total_fee'],'date'=>date('Y-m-d',time())],true);
            }
        });
    }

    public function getStudentTextAttr($value,$data)
    {
        $value = $value ? $value : $data['student_id'];
        return db('student')->where('id',$value)->value('username');
    }

    public function getLessonTextAttr($value,$data)
    {
        $value = $value ? $value : $data['lesson_id'];
        return db('lesson')->where('id',$value)->value('name');
    }

    public function getCreatorTextAttr($value,$data)
    {
        $value = $value ? $value : $data['creator'];
        return model('user')->where('id',$value)->value('username');
    }

    public static function makeSno()
    {
        $count=Contract::whereTime('createtime','today')->count();
        if ($count<1000){
            if ($count>100){
                $count='0'.$count;
            }elseif ($count>10){
                $count='00'.$count;
            }elseif ($count>0){
                $count='000'.$count;
            }elseif ($count==0){
                $count='0001';
            }
        }
        return 'CT'.date('Ymd').$count;
    }



    public function getRestDayAttr($value,$data)
    {
        return ceil((strtotime($data['enddate'])-time())/(3600*24));
    }

    public function getStatusTextAttr($value,$data)
    {
        $list=[0=>'失效',1=>'正常'];
        return $list[$data['status']];
    }
}