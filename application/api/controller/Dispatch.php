<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 12:17
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\SheduleDispatch;
use app\common\model\SheduleVacation;

/**
 * 请假调课
 * Class Dispatch
 * @package app\api\controller
 */
class Dispatch extends Api{

    protected $noNeedLogin=[];
    protected $noNeedRight='*';

    /**
     * 获取请假记录
     * @ApiMethod   (GET)
     * @ApiParams   (name="page", type="int", required=true, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=true, description="分页大小，默认20")
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiParams   (name="teacher_id", type="int", required=true, description="老师id")
     * @ApiParams   (name="status", type="int", required=true, description="状态：1未处理，2已处理")
     * @ApiReturnParams   (name="id", type="int", required=true, description="请假记录id")
     * @ApiReturnParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiReturnParams   (name="date", type="date", required=true, description="课节日期")
     * @ApiReturnParams   (name="banji_lesson_id", type="int", required=true, description="班课id")
     * @ApiReturnParams   (name="lesson_id", type="int", required=true, description="课程id")
     * @ApiReturnParams   (name="teacher_id", type="int", required=true, description="教师id")
     * @ApiReturnParams   (name="shedule_id", type="int", required=true, description="课节id")
     * @ApiReturnParams   (name="reason", type="string", required=true, description="请假原因")
     * @ApiReturnParams   (name="status", type="int", required=true, description="常见status_text")
     * @ApiReturnParams   (name="status_text", type="string", required=true, description="状态，包含：0=>'忽略',1=>'待审核',2=>'已同意',3=>'已调课'")
     * @ApiReturnParams   (name="student", type="string", required=true, description="学员姓名")
     * @ApiReturnParams   (name="lesson", type="string", required=true, description="课程名称")
     * @ApiReturnParams   (name="mobile", type="string", required=true, description="手机号")
     * @ApiReturnParams   (name="shedule_info", type="array", required=true, description="课程信息")
     * @ApiReturnParams   (name="creator_text", type="string", required=true, description="创建人")
     * @ApiReturnParams   (name="updator_text", type="string", required=true, description="更新人")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1523867053','data':{'total':1,'per_page':15,'current_page':1,'last_page':1,'data':[{'id':1,'student_id':1,'date':'2018-04-09','banji_lesson_id':14,'lesson_id':1,'teacher_id':1,'shedule_id':23,'reason':'测试','status':1,'from':'家长端','creator':11,'updator':0,'createtime':'2018-04-09 13:09','updatetime':'2018-04-09 13:09','status_text':'待审核','student':'开发测试','lesson':'尤克里里弹唱','mobile':'15107141306','shedule_info':{'id':23,'teacher_id':1,'banji_lesson_id':14,'lesson_id':1,'date':'2018-04-09','week':'星期一','banji_id':14,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-08 14:57','updatetime':'2018-04-09 13:18','teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'','class_room_id':5,'minute':60,'creator_text':'ladder','banji_type':0},'creator_text':'ladder','updator_text':''}]}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/9 12:34
     */
    public function get_list()
    {
        $page=$this->request->request('page',1,'intval');
        $page_size=$this->request->request('page_size',20,'intval');
        $student_id=$this->request->request('student_id',0,'intval');
        $teacher_id=$this->request->request('teacher_id',0,'intval');
        $status=$this->request->request('status',1);
        $map=[];
        $map['agency_id']=$this->auth->agency_id;
        if ($student_id){$map['student_id']=$student_id;}
        if ($teacher_id){$map['teacher_id']=$teacher_id;}
        if ($status==1){
            $map['status']=$status;
        }elseif($status==2){
            $map['status']=['neq',1];
        }

        $data=model('shedule_vacation')->where($map)->order('id desc')->paginate($page_size,[],['page'=>$page])->jsonSerialize();

        $this->success('查询成功',$data);
    }

    /**
     * 提交申请请假
     * @ApiMethod   (POST)
     * @ApiParams   (name="shedule_id", type="int", required=true, description="课节id")
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiParams   (name="reason", type="string", required=true, description="请假原因")
     * @ApiParams   (name="from", type="int", required=true, description="来源：1家长，2:老师")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/9 11:01
     */
    public function add_vacation()
    {
        $shedule_id=$this->request->post('shedule_id',0,'intval');
        $student_id=$this->request->post('student_id',0,'intval');
        $reason=$this->request->post('reason','','strval');
        $from=$this->request->post('from',0,'intval');
        $creator=$this->auth->id;
        if (empty($shedule_id)||empty($student_id)||empty($reason)||empty($from)){$this->error('参数错误');}
        $shedule_info=\app\common\model\Shedule::get($shedule_id);
        $data=[
            'student_id'=>$student_id,
            'date'=>$shedule_info['date'],
            'banji_lesson_id'=>$shedule_info['banji_lesson_id'],
            'lesson_id'=>$shedule_info['lesson_id'],
            'teacher_id'=>$shedule_info['teacher_id'],
            'shedule_id'=>$shedule_id,
            'reason'=>$reason,
            'status'=>1,
            'from'=>$from,
            'creator'=>$creator
        ];
        if (empty(\app\common\model\BanjiStudent::get(['student_id'=>$student_id,'banji_lesson_id'=>$shedule_info['banji_lesson_id']]))){
            $this->error('您暂未加入该班课，无权限操作');
        }
        if (SheduleVacation::get(['student_id'=>$student_id,'shedule_id'=>$shedule_id])){
            $this->error('你已经有申请请假记录了，不能再次申请');
        }
        $data['agency_id']=$this->auth->agency_id;
        $info=SheduleVacation::create($data);
        if ($info){
            $this->success('提交成功');
        }else{
            $this->error('提交失败');
        }
    }

    /**
     * 教务端请假条处理
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="请假条id")
     * @ApiParams   (name="status", type="int", required=true, description="状态：0忽略，1待审核，2审核通过")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/9 11:08
     */
    public function edit()
    {
        $id=$this->request->post('id');
        $status=$this->request->post('status');
        $rule=['id'=>'require|gt:0','status'=>'require|between:0,2'];
        $data=['id'=>$id,'status'=>$status];
        $validate=new \think\Validate($rule,[],['id'=>'请假条','status'=>'处理状态']);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            $info=SheduleVacation::update(['status'=>$status,'updator'=>$this->auth->id],['id'=>$id]);
            if ($info){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }
    }

    /**
     * 教务端调课
     * @ApiMethod   (POST)
     * @ApiParams   (name="shedule_id", type="int", required=true, description="课节id")
     * @ApiParams   (name="banji_id", type="int", required=true, description="班级id")
     * @ApiParams   (name="banji_lesson_id", type="int", required=true, description="班课id")
     * @ApiParams   (name="lesson_id", type="int", required=true, description="课程id")
     * @ApiParams   (name="teacher_id", type="int", required=true, description="教师id")
     * @ApiParams   (name="date", type="date", required=true, description="日期")
     * @ApiParams   (name="begin_time", type="string", required=true, description="上课开始时间，格式：HH:ii")
     * @ApiParams   (name="end_time", type="string", required=true, description="上课结束时间，格式：HH:ii")
     * @ApiParams   (name="class_room", type="int", required=true, description="教室id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/9 11:59
     */
    public function change_shedule()
    {
        $shedule_id=$this->request->post('shedule_id',0,'intval');
        $banji_id=$this->request->post('banji_id',0,'intval');
        $banji_lesson_id=$this->request->post('banji_lesson_id',0,'intval');
        $lesson_id=$this->request->post('lesson_id',0,'intval');
        $teacher_id=$this->request->post('teacher_id',0,'intval');
        $date=$this->request->post('date');
        $begin_time=$this->request->post('begin_time');
        $end_time=$this->request->post('end_time');
        $class_room=$this->request->post('class_room');
        $rule=[
            'shedule_id'=>'require|gt:0',
            'banji_id'=>'require|gt:0',
            'banji_lesson_id'=>'require|gt:0',
            'lesson_id'=>'require|gt:0',
            'teacher_id'=>'require|gt:0',
            'date'=>'require|date',
            'begin_time'=>'require',
            'end_time'=>'require',
            'class_room'=>'require'
        ];
        $msg=['shedule_id'=>'课节','banji_id'=>'班级','banji_lesson_id'=>'班课','lesson_id'=>'课程',
                'teacher_id'=>'老师','date'=>'日期','begin_time'=>'开始时间','end_time'=>'结束时间',
            'class_room'=>'教室'
            ];
        $data=[
            'shedule_id'=>$shedule_id,'banji_id'=>$banji_id,'banji_lesson_id'=>$banji_lesson_id,
            'lesson_id'=>$lesson_id, 'teacher_id'=>$teacher_id, 'date'=>$date,
            'begin_time'=>format_string_time($begin_time), 'end_time'=>format_string_time($end_time),'class_room'=>$class_room,'updator'=>$this->auth->id
        ];
        $validate=new \think\Validate($rule,[],$msg);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            $info=SheduleDispatch::create($data,true);
            if ($info){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }
    }
}