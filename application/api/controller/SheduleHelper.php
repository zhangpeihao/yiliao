<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/8 18:23
 */
namespace app\api\controller;

use app\common\controller\Api;
use think\helper\Time;

/**
 * 课务助手
 * Class SheduleHelper
 * @package app\api\controller
 */
class SheduleHelper extends Api{

    protected $noNeedRight='*';
    /**
     * 获取当天课务数据
     * @ApiMethod   (GET)
     * @ApiParams   (name="date", type="date", required=false, description="指定日期，默认为当天")
     * @ApiReturnParams   (name="un_lesson", type="int", required=true, description="未接课课程数")
     * @ApiReturnParams   (name="finish_lesson", type="int", required=true, description="已结课课程数")
     * @ApiReturnParams   (name="un_sign", type="int", required=true, description="未签到")
     * @ApiReturnParams   (name="finish_sign", type="int", required=true, description="已签到")
     * @ApiReturnParams   (name="vacation_sign", type="int", required=true, description="请假")
     * @ApiReturnParams   (name="finish_comment", type="int", required=true, description="已课评")
     * @ApiReturnParams   (name="un_comment", type="int", required=true, description="未课评")
     * @ApiReturnParams   (name="ing_teacher", type="int", required=true, description="有课老师")
     * @ApiReturnParams   (name="free_teacher", type="int", required=true, description="无课老师")
     * @ApiReturnParams   (name="unsign_student", type="int", required=true, description="未签约学员")
     * @ApiReturnParams   (name="rest_two", type="int", required=true, description="剩余课时小于2的学员")
     * @ApiReturnParams   (name="rest_lt_seven", type="int", required=true, description="合约到期天数小于7天的学员")
     * @ApiReturnParams   (name="un_shedule_student", type="int", required=true, description="未排课学员")
     * @ApiReturnParams   (name="undeal_vacation", type="int", required=true, description="请假待处理")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1523185883','data':{'un_lesson':5,'finish_lesson':0,'un_sign':3,'finish_sign':0,'vacation_sign':0,'finish_comment':0,'un_comment':5,'ing_teacher':1,'free_teacher':4,'unsign_student':0,'rest_two':0,'rest_lt_seven':0,'un_shedule_student':-1,'undeal_vacation':0}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/9 12:03
     */
    public function get_index()
    {
        $date=$this->request->request('date',date('Y-m-d',time()));
        $return_data=[];
        $agency_id=$this->auth->agency_id;
        $all_shedule_list=(array)db('Shedule')->where(['date'=>$date,'agency_id'=>$agency_id])->group('date,begin_time')->field('id,banji_id,banji_lesson_id,teacher_id')->select();
        $all_shedule=count($all_shedule_list);
        $all_banji_lesson_ids=array_unique(array_column($all_shedule_list,'banji_lesson_id'));
        $all_teacher_id=array_unique(array_column($all_shedule_list,'teacher_id'));

        $un_finish_shedule=db('Shedule')->where(['date'=>$date,'status'=>1,'agency_id'=>$agency_id])->group('date,begin_time')->field('id,banji_id,banji_lesson_id,teacher_id')->select();
        $un_shedule=count($un_finish_shedule);
        $banji_lesson_ids=array_unique(array_column($un_finish_shedule,'banji_lesson_id'));
        $teacher_id=array_unique(array_column($un_finish_shedule,'teacher_id'));

        $student=db('shedule')->where('agency_id',$agency_id)->where('date',$date)->count();
        $sign_student=db('student_sign')->where('agency_id',$agency_id)->whereTime('createtime','between',[strtotime($date),strtotime($date.' 23:59:59')])->where('status',1)->count();
        $vacation_student=db('student_sign')->where('agency_id',$agency_id)->whereTime('createtime','between',[strtotime($date),strtotime($date.' 23:59:59')])->where('status',2)->count();

        $comment=db('shedule_comment')->where('agency_id',$agency_id)
                ->whereTime('createtime','between',[strtotime($date),strtotime($date.' 23:59:59')])->count();

        $teacher=db('teacher')->where('agency_id',$agency_id)->where('status',1)->count();

        $student_count=db('student')->where('agency_id',$agency_id)->where('status',1)->count();
        $banji_student=db('banji_student')->where('agency_id',$agency_id)->distinct(true)->column('student_id');

        //未接课课程数
        $return_data['un_lesson']=$un_shedule;
        //已结课课程数
        $return_data['finish_lesson']=$all_shedule-$un_shedule;
        //未签到
        $return_data['un_sign']=$student-$sign_student;
        //已签到
        $return_data['finish_sign']=$sign_student;
        //请假
        $return_data['vacation_sign']=$vacation_student;
        //已课评
        $return_data['finish_comment']=$comment;
        //未课评
        $return_data['un_comment']=$all_shedule-$comment;

        //有课老师
        $return_data['ing_teacher']=count($all_teacher_id);
        //无课老师
        $return_data['free_teacher']=$teacher-$return_data['ing_teacher'];

        //未签约学员
        $return_data['unsign_student']=db('student')->where('agency_id',$agency_id)->where('status',2)->count();
        //剩余课时小于2的学员
        $return_data['rest_two']=0;

        //合约到期天数小于7天的学员
        $return_data['rest_lt_seven']=db('contract')->where('enddate','between',[date('Y-m-d',time()),date('Y-m-d',strtotime('+7 day'))])
                                        ->where('agency_id',$agency_id)->field('student_id')->distinct(true)->count();

        //未排课学员
        $return_data['un_shedule_student']=$student_count-count($banji_student);

        //请假待处理
        $return_data['undeal_vacation']=$vacation_student;

        $this->success('查询成功',$return_data);

    }
}