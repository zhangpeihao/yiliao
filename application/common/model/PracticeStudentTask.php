<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/26 18:01
 */
namespace app\common\model;

use think\Model;

class PracticeStudentTask extends Model{
    protected $name='practice_student_task';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';


    public static function init()
    {
        PracticeStudentTask::event('after_insert',function ($task){
            //1=>'发布练习视频',2=>'商城消费兑换'
            $score_data=[
                'uid'=>$task['uid'],
                'num'=>10,
                'operate'=>1,
                'type'=>1,
                'creator'=>'system',
                'link_id'=>$task['id'],
                'remark'=>'发布练习视频'
            ];
            $res_info=UserScore::changeScore($score_data);
        });
    }
    
}