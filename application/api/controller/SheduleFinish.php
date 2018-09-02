<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/11 15:35
 */
namespace app\api\controller;

use app\common\controller\Api;
use think\helper\Time;

/**
 * 消课记录
 * Class SheduleFinish
 * @package app\api\controller
 */
class SheduleFinish extends Api{

    protected $noNeedRight='*';
    protected $noNeedLogin='*';

    /**
     * 查询消课记录
     * @ApiMethod   (GET)
     * @ApiParams   (name="page", type="int", required=true, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=true, description="每页数据条数")
     * @ApiParams   (name="student_id", type="int", required=false, description="学员id")
     * @ApiParams   (name="shedule_id", type="int", required=false, description="课节id")
     * @ApiParams   (name="teacher_id", type="int", required=false, description="老师id")
     * @ApiParams   (name="banji_lesson_id", type="int", required=false, description="班课id")
     * @ApiParams   (name="lesson_id", type="int", required=false, description="课程id")
     * @ApiParams   (name="date", type="date", required=false, description="日期")
     * @ApiParams   (name="creator", type="int", required=false, description="消课人id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/11 15:45
     */
    public function get_list()
    {
        $student_id=$this->request->get('student_id',0,'intval');
        $shedule_id=$this->request->get('shedule_id',0,'intval');
        $teacher_id=$this->request->get('teacher_id',0,'intval');
        $banji_lesson_id=$this->request->get('banji_lesson_id',0,'intval');
        $lesson_id=$this->request->get('lesson_id',0,'intval');
        $date=$this->request->get('date','');
        $creator=$this->request->get('creator',0,'intval');
        $page=$this->request->request('page',1);
        $page_size=$this->request->request('page_size',20,'intval');
        $map=[];
        $map['agency_id']=$this->auth->agency_id;
        if ($student_id){$map['student_id']=$student_id;}
        if ($shedule_id){$map['shedule_id']=$shedule_id;}
        if ($teacher_id){$map['teacher_id']=$teacher_id;}
        if ($banji_lesson_id){$map['banji_lesson_id']=$banji_lesson_id;}
        if ($lesson_id){$map['lesson_id']=$lesson_id;}
        if ($date!=''){$map['date']=$date;}
        if ($creator){$map['creator']=$creator;}
        $data=model('SheduleFinish')->where($map)->order('id desc')->paginate($page_size,[],['page'=>$page])->jsonSerialize();

        $this->success('查询成功',$data);
    }


    /**
     * 按老师获取消课记录
     * @ApiMethod   (GET)
     * @ApiParams   (name="date", type="date", required=true, description="查询日期：默认当天")
     * @ApiParams   (name="teacher_id", type="date", required=false, description="查询指定老师的id")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1523518339','data':[{'teacher_name':'余','shedule':[{'id':14,'teacher_id':1,'banji_lesson_id':1,'lesson_id':0,'date':'2018-04-11','week':'星期三','banji_id':4,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-07 22:06','updatetime':'2018-04-09 13:18','student_count':0,'un_sign':0,'un_comment':0,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'','banji_name':'开发测试','class_room_id':5,'minute':0,'creator_text':'ladder','banji_type':2},{'id':24,'teacher_id':1,'banji_lesson_id':14,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':14,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-08 14:57','updatetime':'2018-04-09 13:18','student_count':1,'un_sign':0,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'','class_room_id':5,'minute':60,'creator_text':'ladder','banji_type':null},{'id':34,'teacher_id':1,'banji_lesson_id':15,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':15,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-08 14:58','updatetime':'2018-04-09 13:18','student_count':1,'un_sign':1,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'','class_room_id':5,'minute':60,'creator_text':'ladder','banji_type':null},{'id':44,'teacher_id':1,'banji_lesson_id':16,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':16,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-08 14:59','updatetime':'2018-04-09 13:18','student_count':1,'un_sign':1,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'开发测试','class_room_id':5,'minute':60,'creator_text':'ladder','banji_type':2},{'id':82,'teacher_id':1,'banji_lesson_id':28,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':28,'begin_time':'18:12','end_time':'19:17','dec_num':1,'class_room':'三年6班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'','createtime':'2018-04-09 18:13','updatetime':'2018-04-09 18:13','student_count':1,'un_sign':1,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'滴滴出行','class_room_id':6,'minute':45,'creator_text':'ladder','banji_type':2}],'count':5,'finish':0},{'teacher_name':'刘','shedule':[{'id':101,'teacher_id':4,'banji_lesson_id':52,'lesson_id':4,'date':'2018-04-11','week':'星期三','banji_id':29,'begin_time':'08:00','end_time':'09:00','dec_num':1,'class_room':'','status':0,'dispatch_id':0,'creator':'小鱼儿','updator':0,'remark':'','createtime':'2018-04-10 15:36','updatetime':'2018-04-10 15:47','student_count':0,'un_sign':0,'un_comment':0,'teacher_name':'刘','status_text':'禁用','dispatch':{},'lesson':'rRNA','banji_name':'呀呀呀','class_room_id':0,'minute':60,'creator_text':'小鱼儿','banji_type':1},{'id':106,'teacher_id':4,'banji_lesson_id':53,'lesson_id':10,'date':'2018-04-11','week':'星期三','banji_id':29,'begin_time':'15:54','end_time':'16:54','dec_num':1,'class_room':'5年级','status':2,'dispatch_id':0,'creator':'ladder','updator':10,'remark':'测试','createtime':'2018-04-10 15:54','updatetime':'2018-04-12 12:36','student_count':5,'un_sign':0,'un_comment':2,'teacher_name':'刘','status_text':'已结课','dispatch':{},'lesson':'三弦乐','banji_name':'呀呀呀','class_room_id':8,'minute':60,'creator_text':'ladder','banji_type':1}],'count':2,'finish':5}]}")
     * @ApiReturnParams   (name="shuld_finish", type="string", required=true, description="应该消课节数")
     * @ApiReturnParams   (name="real_finish", type="string", required=true, description="实际消课节数")
     * @ApiReturnParams   (name="data：teacher_id", type="string", required=true, description="教师姓名")
     * @ApiReturnParams   (name="data：teacher_id", type="int", required=true, description="教师id")
     * @ApiReturnParams   (name="data：shedule_count", type="int", required=true, description="课节总数")
     * @ApiReturnParams   (name="data：total_finish", type="int", required=true, description="已结课数")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1523525234','data':{'shuld_finish':62,'real_finish':5,'detail':[{'teacher_id':1,'teacher_name':'余','shedule_count':43,'total_finish':0},{'teacher_id':5,'teacher_name':'红包','shedule_count':8,'total_finish':0},{'teacher_id':3,'teacher_name':'洪','shedule_count':1,'total_finish':0},{'teacher_id':2,'teacher_name':'王','shedule_count':6,'total_finish':0},{'teacher_id':4,'teacher_name':'刘','shedule_count':4,'total_finish':5}]}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/12 14:47
     */
    public function get_by_teacher()
    {
        $date=$this->request->request('date',date('Y-m-d',time()));
        $teacher_id=$this->request->request('teacher_id',0,'intval');
        list($starttime,$endtime)=Time::month();
        $startdate=date('Y-m-d',$starttime);
        $enddate=date('Y-m-d',$endtime);
        $map['date']=['between',[$startdate,$enddate]];
        $map['status']=['neq',0];
        $map['agency_id']=$this->auth->agency_id;
        if (empty($teacher_id)){
            $teacher=db('Shedule')->where($map)->distinct(true)->column('teacher_id');
        }else{
            $teacher=[$teacher_id];
        }
        $data=[];
        foreach ($teacher as $v){
            $teacher_name=db('teacher')->where('id',$v)->value('username');
            $map['teacher_id']=$v;
            $shedule=model('shedule')->where($map)->group('date,begin_time')->order('begin_time asc')->count();
            $finish=db('shedule_finish')->where($map)->count();
            $data[]=[
                'teacher_id'=>$v,
                'teacher_name'=>$teacher_name,
                'shedule_count'=>$shedule,
                'total_finish'=>$finish
            ];
        }
        $total=[
            'shuld_finish'=>array_sum(array_column($data,'shedule_count')),
            'real_finish'=>array_sum(array_column($data,'total_finish')),
            'detail'=>$data
        ];
        $this->success('查询成功',$total);
    }


    /**
     * 按课程获取消课记录
     * @ApiMethod   (GET)
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1523518372','data':[{'banji_lesson':{'id':14,'banji_id':14,'class_room':'二年1班','lesson_id':1,'teacher_id':1,'startdate':'2018-04-07','begin_time':'13:30','minute':60,'end_time':'14:30','dec_num':1,'frequency':2,'frequency_week':'1,3,4','lesson_count':10,'remark':'开发测试','status':1,'creator':11,'updator':0,'createtime':'2018-04-08 14:57','updatetime':'2018-04-08 14:57','lesson':'尤克里里弹唱','teacher':'余','enddate':'2018-04-25','status_text':'未接课','frequency_text':'隔天','week':'星期六','class_room_id':5,'banji_info':[]},'shedule':[{'id':24,'teacher_id':1,'banji_lesson_id':14,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':14,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-08 14:57','updatetime':'2018-04-09 13:18','student_count':1,'un_sign':0,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'','class_room_id':5,'minute':60,'creator_text':'ladder','banji_type':null}],'count':1,'finish':0},{'banji_lesson':{'id':15,'banji_id':15,'class_room':'二年1班','lesson_id':1,'teacher_id':1,'startdate':'2018-04-07','begin_time':'13:30','minute':60,'end_time':'14:30','dec_num':1,'frequency':2,'frequency_week':'1,3,4','lesson_count':10,'remark':'开发测试','status':1,'creator':11,'updator':0,'createtime':'2018-04-08 14:58','updatetime':'2018-04-08 14:58','lesson':'尤克里里弹唱','teacher':'余','enddate':'2018-04-25','status_text':'未接课','frequency_text':'隔天','week':'星期六','class_room_id':5,'banji_info':[]},'shedule':[{'id':34,'teacher_id':1,'banji_lesson_id':15,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':15,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-08 14:58','updatetime':'2018-04-09 13:18','student_count':1,'un_sign':1,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'','class_room_id':5,'minute':60,'creator_text':'ladder','banji_type':null}],'count':1,'finish':0},{'banji_lesson':{'id':16,'banji_id':16,'class_room':'二年1班','lesson_id':1,'teacher_id':1,'startdate':'2018-04-07','begin_time':'13:30','minute':60,'end_time':'14:30','dec_num':1,'frequency':2,'frequency_week':'1,3,4','lesson_count':10,'remark':'开发测试','status':1,'creator':11,'updator':0,'createtime':'2018-04-08 14:59','updatetime':'2018-04-08 14:59','lesson':'尤克里里弹唱','teacher':'余','enddate':'2018-04-25','status_text':'未接课','frequency_text':'隔天','week':'星期六','class_room_id':5,'banji_info':{'id':16,'type':2,'name':'开发测试','lesson_id':1,'max_member':30,'header_uid':0,'status':1,'creator':11,'updator':0,'remark':'开发测试','createtime':1523170758,'updatetime':1523170758}},'shedule':[{'id':44,'teacher_id':1,'banji_lesson_id':16,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':16,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-08 14:59','updatetime':'2018-04-09 13:18','student_count':1,'un_sign':1,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'开发测试','class_room_id':5,'minute':60,'creator_text':'ladder','banji_type':2}],'count':1,'finish':0},{'banji_lesson':{'id':28,'banji_id':28,'class_room':'三年6班','lesson_id':1,'teacher_id':1,'startdate':'2018-04-09','begin_time':'18:12','minute':45,'end_time':'19:17','dec_num':1,'frequency':1,'frequency_week':'','lesson_count':5,'remark':'','status':1,'creator':11,'updator':0,'createtime':'2018-04-09 18:13','updatetime':'2018-04-09 18:13','lesson':'尤克里里弹唱','teacher':'余','enddate':'2018-04-13','status_text':'未接课','frequency_text':'每天','week':'星期一','class_room_id':6,'banji_info':{'id':28,'type':2,'name':'滴滴出行','lesson_id':1,'max_member':1,'header_uid':0,'status':1,'creator':11,'updator':0,'remark':'','createtime':1523268781,'updatetime':1523268781}},'shedule':[{'id':82,'teacher_id':1,'banji_lesson_id':28,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':28,'begin_time':'18:12','end_time':'19:17','dec_num':1,'class_room':'三年6班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'','createtime':'2018-04-09 18:13','updatetime':'2018-04-09 18:13','student_count':1,'un_sign':1,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'滴滴出行','class_room_id':6,'minute':45,'creator_text':'ladder','banji_type':2}],'count':1,'finish':0},{'banji_lesson':{'id':53,'banji_id':29,'class_room':'5年级','lesson_id':10,'teacher_id':4,'startdate':'2018-04-10','begin_time':'15:54','minute':60,'end_time':'16:54','dec_num':1,'frequency':1,'frequency_week':'','lesson_count':5,'remark':'测试','status':1,'creator':11,'updator':0,'createtime':'2018-04-10 15:54','updatetime':'2018-04-10 15:54','lesson':'三弦乐','teacher':'刘','enddate':'2018-04-14','status_text':'未接课','frequency_text':'每天','week':'星期二','class_room_id':8,'banji_info':{'id':29,'type':1,'name':'呀呀呀','lesson_id':4,'max_member':45,'header_uid':4,'status':2,'creator':11,'updator':0,'remark':'测试一下','createtime':1523268828,'updatetime':1523515732}},'shedule':[{'id':106,'teacher_id':4,'banji_lesson_id':53,'lesson_id':10,'date':'2018-04-11','week':'星期三','banji_id':29,'begin_time':'15:54','end_time':'16:54','dec_num':1,'class_room':'5年级','status':2,'dispatch_id':0,'creator':'ladder','updator':10,'remark':'测试','createtime':'2018-04-10 15:54','updatetime':'2018-04-12 12:36','student_count':5,'un_sign':0,'un_comment':2,'teacher_name':'刘','status_text':'已结课','dispatch':{},'lesson':'三弦乐','banji_name':'呀呀呀','class_room_id':8,'minute':60,'creator_text':'ladder','banji_type':1}],'count':1,'finish':5}]}")
     * @ApiReturnParams   (name="real_finish", type="int", required=true, description="实际消课")
     * @ApiReturnParams   (name="shuld_finish", type="int", required=true, description="应该消课")
     * @ApiReturnParams   (name="data：lesson_id", type="int", required=true, description="课程id")
     * @ApiReturnParams   (name="data：lesson", type="string", required=true, description="课程名称")
     * @ApiReturnParams   (name="data：shedule_count", type="int", required=true, description="总课节数")
     * @ApiReturnParams   (name="data：total_finish", type="int", required=true, description="已结课数")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1523525369','data':{'shuld_finish':54,'real_finish':5,'detail':[{'lesson_id':1,'lesson':'尤克里里弹唱','shedule_count':39,'total_finish':0},{'lesson_id':5,'lesson':'测试','shedule_count':4,'total_finish':0},{'lesson_id':2,'lesson':'爵士鼓初级16课','shedule_count':1,'total_finish':0},{'lesson_id':3,'lesson':'民谣弹唱初级16课','shedule_count':2,'total_finish':0},{'lesson_id':8,'lesson':'王者荣耀体验服','shedule_count':5,'total_finish':0},{'lesson_id':10,'lesson':'三弦乐','shedule_count':2,'total_finish':5},{'lesson_id':4,'lesson':'rRNA','shedule_count':1,'total_finish':0}]}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/12 15:09
     */
    public function get_by_banji_lesson()
    {
        $date=$this->request->request('date',date('Y-m-d',time()));
        list($starttime,$endtime)=Time::month();
        $startdate=date('Y-m-d',$starttime);
        $enddate=date('Y-m-d',$endtime);
        $map['date']=['between',[$startdate,$enddate]];
        $map['status']=['neq',0];
        $map['agency_id']=$this->auth->agency_id;
        $lesson_id=db('Shedule')->where($map)->distinct(true)->column('lesson_id');
        $data=[];
        foreach ($lesson_id as $v){
            $lesson=model('lesson')->where('id',$v)->find();
            if (empty($lesson)){
                continue;
            }
            $map['lesson_id']=$v;
            $shedule=model('shedule')->where($map)->order('begin_time asc')->count();
            $finish=db('shedule_finish')->where($map)->count();
            $data[]=[
                'lesson_id'=>$v,
                'lesson'=>$lesson['name'],
                'shedule_count'=>$shedule,
                'total_finish'=>$finish
            ];
        }
        $total=[
            'shuld_finish'=>array_sum(array_column($data,'shedule_count')),
            'real_finish'=>array_sum(array_column($data,'total_finish')),
            'detail'=>$data
        ];
        $this->success('查询成功',$total);
    }

}