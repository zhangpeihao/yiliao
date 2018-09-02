<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/12 14:46
 */
namespace app\api\controller;

use app\common\controller\Api;


/**
 * 课表分类查询
 * Class SheduleSearch
 * @package app\api\controller
 */
class SheduleSearch extends Api{

    protected $noNeedRight='*';

    /**
     * 按老师
     * @ApiMethod   (GET)
     * @ApiParams   (name="date", type="date", required=false, description="指定查询日期：默认当天")
     * @ApiParams   (name="startdate", type="date", required=false, description="查询日期起始范围")
     * @ApiParams   (name="enddate", type="date", required=false, description="查询日期结束时间")
     * @ApiParams   (name="teacher_id", type="int", required=false, description="指定查询老师的id")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1523518339','data':[{'teacher_name':'余','shedule':[{'id':14,'teacher_id':1,'banji_lesson_id':1,'lesson_id':0,'date':'2018-04-11','week':'星期三','banji_id':4,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-07 22:06','updatetime':'2018-04-09 13:18','student_count':0,'un_sign':0,'un_comment':0,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'','banji_name':'开发测试','class_room_id':5,'minute':0,'creator_text':'ladder','banji_type':2},{'id':24,'teacher_id':1,'banji_lesson_id':14,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':14,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-08 14:57','updatetime':'2018-04-09 13:18','student_count':1,'un_sign':0,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'','class_room_id':5,'minute':60,'creator_text':'ladder','banji_type':null},{'id':34,'teacher_id':1,'banji_lesson_id':15,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':15,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-08 14:58','updatetime':'2018-04-09 13:18','student_count':1,'un_sign':1,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'','class_room_id':5,'minute':60,'creator_text':'ladder','banji_type':null},{'id':44,'teacher_id':1,'banji_lesson_id':16,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':16,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-08 14:59','updatetime':'2018-04-09 13:18','student_count':1,'un_sign':1,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'开发测试','class_room_id':5,'minute':60,'creator_text':'ladder','banji_type':2},{'id':82,'teacher_id':1,'banji_lesson_id':28,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':28,'begin_time':'18:12','end_time':'19:17','dec_num':1,'class_room':'三年6班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'','createtime':'2018-04-09 18:13','updatetime':'2018-04-09 18:13','student_count':1,'un_sign':1,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'滴滴出行','class_room_id':6,'minute':45,'creator_text':'ladder','banji_type':2}],'count':5,'finish':0},{'teacher_name':'刘','shedule':[{'id':101,'teacher_id':4,'banji_lesson_id':52,'lesson_id':4,'date':'2018-04-11','week':'星期三','banji_id':29,'begin_time':'08:00','end_time':'09:00','dec_num':1,'class_room':'','status':0,'dispatch_id':0,'creator':'小鱼儿','updator':0,'remark':'','createtime':'2018-04-10 15:36','updatetime':'2018-04-10 15:47','student_count':0,'un_sign':0,'un_comment':0,'teacher_name':'刘','status_text':'禁用','dispatch':{},'lesson':'rRNA','banji_name':'呀呀呀','class_room_id':0,'minute':60,'creator_text':'小鱼儿','banji_type':1},{'id':106,'teacher_id':4,'banji_lesson_id':53,'lesson_id':10,'date':'2018-04-11','week':'星期三','banji_id':29,'begin_time':'15:54','end_time':'16:54','dec_num':1,'class_room':'5年级','status':2,'dispatch_id':0,'creator':'ladder','updator':10,'remark':'测试','createtime':'2018-04-10 15:54','updatetime':'2018-04-12 12:36','student_count':5,'un_sign':0,'un_comment':2,'teacher_name':'刘','status_text':'已结课','dispatch':{},'lesson':'三弦乐','banji_name':'呀呀呀','class_room_id':8,'minute':60,'creator_text':'ladder','banji_type':1}],'count':2,'finish':5}]}")
     * @ApiReturnParams   (name="teacher_name", type="string", required=true, description="教师姓名")
     * @ApiReturnParams   (name="teacher_id", type="int", required=true, description="教师id")
     * @ApiReturnParams   (name="shedule", type="array", required=true, description="课程记录，结构同课节安排接口")
     * @ApiReturnParams   (name="count", type="int", required=true, description="总课节数")
     * @ApiReturnParams   (name="finish", type="int", required=true, description="已结课数")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/12 14:47
     */
    public function get_by_teacher()
    {
        $date=$this->request->request('date',date('Y-m-d',time()));
        $startdate=$this->request->request('startdate');
        $enddate=$this->request->request('enddate');
        $teacher_id=$this->request->request('teacher_id');
        if ($date){
            $map['date']=$date;
        }
        if ($startdate){
            $map['date']=['between',[$startdate,$enddate]];
        }
        if ($teacher_id){
            $map['teacher_id']=$teacher_id;
        }
        $map['agency_id']=$this->auth->agency_id;
        $map['status']=['neq',0];
        $teacher=db('Shedule')->where($map)->distinct(true)->column('teacher_id');
        $data=[];
        foreach ($teacher as $v){
            $teacher_name=db('teacher')->where('id',$v)->value('username');
            $map['teacher_id']=$v;
            $shedule=model('shedule')->where($map)->group('date,begin_time')->order('begin_time asc')->select();
            foreach ($shedule as &$item){
                //判断是否签到
                $student_count=model('BanjiStudent')->where(['banji_id'=>$item['banji_id'],'banji_lesson_id'=>$item['banji_lesson_id'],'shedule_id'=>0])
                    ->whereOr('shedule_id',$item['id'])
                    ->where('status',1)
                    ->group('student_id')->count();
                $sign_count=model('StudentSign')->where(['banji_id'=>$item['banji_id'],'banji_lesson_id'=>$item['banji_lesson_id']])->count();
                $comment_count=model('Shedule_comment')->where(['banji_id'=>$item['banji_id'],'banji_lesson_id'=>$item['banji_lesson_id']])->count();
                $item['student_count']=$student_count;
                $item['un_sign']=$student_count-$sign_count;
                $item['un_comment']=$student_count-$comment_count;
            }
            $count=count($shedule);
            $finish=db('shedule_finish')->where($map)->count();
            $data[]=[
                'teacher_id'=>$v,
                'teacher_name'=>$teacher_name,
                'shedule'=>$shedule,
                'count'=>$count,
                'finish'=>$finish
            ];
        }
        $this->success('查询成功',$data);
    }


    /**
     * 按课程获取课节安排
     * @ApiMethod   (GET)
     * @ApiParams   (name="date", type="date", required=false, description="查询日期：默认当天")
     * @ApiParams   (name="startdate", type="date", required=false, description="查询日期起始范围")
     * @ApiParams   (name="enddate", type="date", required=false, description="查询日期结束时间")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1523518372','data':[{'banji_lesson':{'id':14,'banji_id':14,'class_room':'二年1班','lesson_id':1,'teacher_id':1,'startdate':'2018-04-07','begin_time':'13:30','minute':60,'end_time':'14:30','dec_num':1,'frequency':2,'frequency_week':'1,3,4','lesson_count':10,'remark':'开发测试','status':1,'creator':11,'updator':0,'createtime':'2018-04-08 14:57','updatetime':'2018-04-08 14:57','lesson':'尤克里里弹唱','teacher':'余','enddate':'2018-04-25','status_text':'未接课','frequency_text':'隔天','week':'星期六','class_room_id':5,'banji_info':[]},'shedule':[{'id':24,'teacher_id':1,'banji_lesson_id':14,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':14,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-08 14:57','updatetime':'2018-04-09 13:18','student_count':1,'un_sign':0,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'','class_room_id':5,'minute':60,'creator_text':'ladder','banji_type':null}],'count':1,'finish':0},{'banji_lesson':{'id':15,'banji_id':15,'class_room':'二年1班','lesson_id':1,'teacher_id':1,'startdate':'2018-04-07','begin_time':'13:30','minute':60,'end_time':'14:30','dec_num':1,'frequency':2,'frequency_week':'1,3,4','lesson_count':10,'remark':'开发测试','status':1,'creator':11,'updator':0,'createtime':'2018-04-08 14:58','updatetime':'2018-04-08 14:58','lesson':'尤克里里弹唱','teacher':'余','enddate':'2018-04-25','status_text':'未接课','frequency_text':'隔天','week':'星期六','class_room_id':5,'banji_info':[]},'shedule':[{'id':34,'teacher_id':1,'banji_lesson_id':15,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':15,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-08 14:58','updatetime':'2018-04-09 13:18','student_count':1,'un_sign':1,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'','class_room_id':5,'minute':60,'creator_text':'ladder','banji_type':null}],'count':1,'finish':0},{'banji_lesson':{'id':16,'banji_id':16,'class_room':'二年1班','lesson_id':1,'teacher_id':1,'startdate':'2018-04-07','begin_time':'13:30','minute':60,'end_time':'14:30','dec_num':1,'frequency':2,'frequency_week':'1,3,4','lesson_count':10,'remark':'开发测试','status':1,'creator':11,'updator':0,'createtime':'2018-04-08 14:59','updatetime':'2018-04-08 14:59','lesson':'尤克里里弹唱','teacher':'余','enddate':'2018-04-25','status_text':'未接课','frequency_text':'隔天','week':'星期六','class_room_id':5,'banji_info':{'id':16,'type':2,'name':'开发测试','lesson_id':1,'max_member':30,'header_uid':0,'status':1,'creator':11,'updator':0,'remark':'开发测试','createtime':1523170758,'updatetime':1523170758}},'shedule':[{'id':44,'teacher_id':1,'banji_lesson_id':16,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':16,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':'二年1班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'开发测试','createtime':'2018-04-08 14:59','updatetime':'2018-04-09 13:18','student_count':1,'un_sign':1,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'开发测试','class_room_id':5,'minute':60,'creator_text':'ladder','banji_type':2}],'count':1,'finish':0},{'banji_lesson':{'id':28,'banji_id':28,'class_room':'三年6班','lesson_id':1,'teacher_id':1,'startdate':'2018-04-09','begin_time':'18:12','minute':45,'end_time':'19:17','dec_num':1,'frequency':1,'frequency_week':'','lesson_count':5,'remark':'','status':1,'creator':11,'updator':0,'createtime':'2018-04-09 18:13','updatetime':'2018-04-09 18:13','lesson':'尤克里里弹唱','teacher':'余','enddate':'2018-04-13','status_text':'未接课','frequency_text':'每天','week':'星期一','class_room_id':6,'banji_info':{'id':28,'type':2,'name':'滴滴出行','lesson_id':1,'max_member':1,'header_uid':0,'status':1,'creator':11,'updator':0,'remark':'','createtime':1523268781,'updatetime':1523268781}},'shedule':[{'id':82,'teacher_id':1,'banji_lesson_id':28,'lesson_id':1,'date':'2018-04-11','week':'星期三','banji_id':28,'begin_time':'18:12','end_time':'19:17','dec_num':1,'class_room':'三年6班','status':1,'dispatch_id':0,'creator':'ladder','updator':0,'remark':'','createtime':'2018-04-09 18:13','updatetime':'2018-04-09 18:13','student_count':1,'un_sign':1,'un_comment':1,'teacher_name':'余','status_text':'未结课','dispatch':{},'lesson':'尤克里里弹唱','banji_name':'滴滴出行','class_room_id':6,'minute':45,'creator_text':'ladder','banji_type':2}],'count':1,'finish':0},{'banji_lesson':{'id':53,'banji_id':29,'class_room':'5年级','lesson_id':10,'teacher_id':4,'startdate':'2018-04-10','begin_time':'15:54','minute':60,'end_time':'16:54','dec_num':1,'frequency':1,'frequency_week':'','lesson_count':5,'remark':'测试','status':1,'creator':11,'updator':0,'createtime':'2018-04-10 15:54','updatetime':'2018-04-10 15:54','lesson':'三弦乐','teacher':'刘','enddate':'2018-04-14','status_text':'未接课','frequency_text':'每天','week':'星期二','class_room_id':8,'banji_info':{'id':29,'type':1,'name':'呀呀呀','lesson_id':4,'max_member':45,'header_uid':4,'status':2,'creator':11,'updator':0,'remark':'测试一下','createtime':1523268828,'updatetime':1523515732}},'shedule':[{'id':106,'teacher_id':4,'banji_lesson_id':53,'lesson_id':10,'date':'2018-04-11','week':'星期三','banji_id':29,'begin_time':'15:54','end_time':'16:54','dec_num':1,'class_room':'5年级','status':2,'dispatch_id':0,'creator':'ladder','updator':10,'remark':'测试','createtime':'2018-04-10 15:54','updatetime':'2018-04-12 12:36','student_count':5,'un_sign':0,'un_comment':2,'teacher_name':'刘','status_text':'已结课','dispatch':{},'lesson':'三弦乐','banji_name':'呀呀呀','class_room_id':8,'minute':60,'creator_text':'ladder','banji_type':1}],'count':1,'finish':5}]}")
     * @ApiReturnParams   (name="banji_lesson", type="array", required=true, description="班课详情,内部新增字段：banji_info班级信息，为空表示班级不存在")
     * @ApiReturnParams   (name="shedule", type="array", required=true, description="课程记录，结构同课节安排接口")
     * @ApiReturnParams   (name="count", type="int", required=true, description="总课节数")
     * @ApiReturnParams   (name="finish", type="int", required=true, description="已结课数")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/12 15:09
     */
    public function get_by_banji_lesson()
    {
        $date=$this->request->request('date',date('Y-m-d',time()));
        $startdate=$this->request->request('startdate');
        $enddate=$this->request->request('enddate');
        if ($date){
            $map['date']=$date;
        }
        if ($startdate){
            $map['date']=['between',[$startdate,$enddate]];
        }
        $map['status']=['neq',0];
        $map['agency_id']=$this->auth->agency_id;
        $banji_lesson_id=db('Shedule')->where($map)->distinct(true)->column('banji_lesson_id');
        $data=[];
        foreach ($banji_lesson_id as $v){
            $banji_lesson=model('banji_lesson')->where('id',$v)->find();
            if (empty($banji_lesson) ||empty($banji_lesson['banji_info'])){
                continue;
            //    $banji_lesson=[];
            }
            $map['banji_lesson_id']=$v;
            $shedule=model('shedule')->where($map)->order('begin_time asc')->select();
            foreach ($shedule as &$item){
                //判断是否签到
                $student_count=model('BanjiStudent')->where(['banji_id'=>$item['banji_id'],'banji_lesson_id'=>$item['banji_lesson_id'],'shedule_id'=>0])
                    ->whereOr('shedule_id',$item['id'])
                    ->where('status',1)
                    ->group('student_id')->count();
                $sign_count=model('StudentSign')->where(['banji_id'=>$item['banji_id'],'banji_lesson_id'=>$item['banji_lesson_id']])->count();
                $comment_count=model('Shedule_comment')->where(['banji_id'=>$item['banji_id'],'banji_lesson_id'=>$item['banji_lesson_id']])->count();
                $item['student_count']=$student_count;
                $item['un_sign']=$student_count-$sign_count;
                $item['un_comment']=$student_count-$comment_count;
            }
            $count=count($shedule);
            $finish=db('shedule_finish')->where($map)->count();
            $data[]=[
                'banji_lesson'=>$banji_lesson,
                'shedule'=>$shedule,
                'count'=>$count,
                'finish'=>$finish
            ];
        }
        $this->success('查询成功',$data);
    }


    /**
     * 查询无课老师
     * @ApiMethod   (GET)
     * @ApiParams   (name="date", type="date", required=true, description="查询日期，默认当天")
     * @ApiParams   (name="begin_time", type="time", required=false, description="开始时间，HH:ii")
     * @ApiParams   (name="end_time", type="time", required=false, description="结束时间，HH:ii")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/12 15:35
     */
    public function get_free_teacher()
    {
        $date=$this->request->request('date');
        $begin_time=$this->request->request('begin_time');
        $end_time=$this->request->request('end_time');
        $map=[];
        if ($date){$map['date']=$date;}else{$map['date']=date('Y-m-d',time());}
        if ($begin_time!=''){$map['begin_time']=['egt',format_string_time($begin_time)];}
        if ($end_time!=''){$map['end_time']=['elt',format_string_time($end_time)];}
        $map['agency_id']=$this->auth->agency_id;
        $teacher=db('shedule')->where($map)->distinct(true)->column('teacher_id');
        $teacher=model('teacher')->where('id','not in',$teacher)->select();
        $this->success('查询成功',$teacher);
    }


}