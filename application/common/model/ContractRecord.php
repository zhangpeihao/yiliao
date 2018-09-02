<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/10 11:02
 */
namespace app\common\model;

use think\Model;

class ContractRecord extends Model{
    protected $name='contract_record';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=['type_text','student','rest_day','rest_shedule','lesson','lesson_info'];

    public function getTypeTextAttr($value,$data)
    {
        $list=[0=>'默认',1=>'新签',2=>'赠送',3=>'续费',4=>'退款'];
        return $list[$data['type']];
    }

    public function getCreatorAttr($value,$data)
    {
        return db('user')->where('id',$value)->value('username');
    }


    public function getStudentAttr($value,$data)
    {
        return db('Student')->where('id',$data['student_id'])->value('username');
    }


    public function getRestDayAttr($value,$data)
    {
        return ceil((strtotime($data['enddate'])-time())/(3600*24));
    }

    public function getRestSheduleAttr($value,$data)
    {
        return floatval($data['lesson_count']);
    }

    public function getLessonAttr($value,$data)
    {
        return (string)db('lesson')->where('id',$data['lesson_id'])->value('name');
    }

    public function getLessonInfoAttr($value,$data)
    {
        $res=\model('Lesson')->where('id',$data['lesson_id'])->find();
        if ($res){
            return $res->toArray();
        }else{
            return (object)[];
        }
    }
}