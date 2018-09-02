<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 12:21
 */
namespace app\api\controller;

use app\admin\model\StudentSign;
use app\common\controller\Api;
use app\common\model\BanjiStudentCancel;
use app\common\model\SheduleFinish;

/**
 * 课节安排
 * Class Shedule
 * @package app\api\controller
 */
class Shedule extends Api{

    protected $noNeedRight='*';

    /**
     * 获取课节安排列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="banji_id", type="int", required=false, description="班级id")
     * @ApiParams   (name="banji_lesson_id", type="int", required=false, description="班课id")
     * @ApiParams   (name="date", type="string", required=false, description="日期，YYYY-mm-dd")
     * @ApiParams   (name="week", type="int", required=false, description="星期")
     * @ApiParams   (name="teacher_id", type="int", required=false, description="教师id")
     * @ApiParams   (name="student_id", type="int", required=false, description="学员id")
     * @ApiParams   (name="lesson_id", type="int", required=false, description="课程id")
     * @ApiParams   (name="class_room", type="int", required=false, description="教师")
     * @ApiParams   (name="not_entry", type="int", required=false, description="未参加的课程，1是，0否，默认为0")
     * @ApiParams   (name="is_today", type="int", required=false, description="是否查询当天，1是，0否，默认为0")
     * @ApiParams   (name="page", type="int", required=false, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=false, description="每页数据条数，默认20")
     * @ApiParams   (name="get_myself", type="int", required=false, description="是否获取我的课程")
     * @ApiReturnParams   (name="id", type="int", required=true, description="课节id，也即后文所需要的：shedule_id")
     * @ApiReturnParams   (name="teacher_id", type="int", required=true, description="教师id")
     * @ApiReturnParams   (name="banji_id", type="int", required=true, description="班级id")
     * @ApiReturnParams   (name="banji_type", type="int", required=true, description="班级类型：1班课，2一对一")
     * @ApiReturnParams   (name="banji_lesson_id", type="int", required=true, description="班课id")
     * @ApiReturnParams   (name="lesson_id", type="int", required=true, description="课程id")
     * @ApiReturnParams   (name="date", type="date", required=true, description="上课日期")
     * @ApiReturnParams   (name="week", type="string", required=true, description="星期")
     * @ApiReturnParams   (name="banji_id", type="int", required=true, description="班级id")
     * @ApiReturnParams   (name="begin_time", type="string", required=true, description="上课时间点",sample="13:30")
     * @ApiReturnParams   (name="end_time", type="string", required=true, description="下课时间点",sample="14:30")
     * @ApiReturnParams   (name="dec_num", type="int", required=true, description="扣课时数")
     * @ApiReturnParams   (name="class_room", type="string", required=true, description="教室")
     * @ApiReturnParams   (name="status", type="int", required=true, description="状态，代表值参考status_text,包含：0=>'禁用',1=>'未结课',2=>'已结课',3=>'已调课'")
     * @ApiReturnParams   (name="status_text", type="string", required=true, description="状态描述")
     * @ApiReturnParams   (name="creator", type="string", required=true, description="创建人")
     * @ApiReturnParams   (name="teacher_name", type="string", required=true, description="教师姓名")
     * @ApiReturnParams   (name="dispatch", type="object", required=true, description="调课记录")
     * @ApiReturnParams   (name="un_sign", type="int", required=true, description="未签到数")
     * @ApiReturnParams   (name="un_comment", type="int", required=true, description="未评课数")
     * @ApiReturnParams   (name="student_count", type="int", required=true, description="学员总数")
     * @ApiReturnParams   (name="student_list", type="array", required=true, description="课节学员列表，包含id,username,mobile,avatar,gender等")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1523179038','data':{'total':4,'per_page':20,'current_page':1,'last_page':1,'data':[{'id':15,'teacher_id':1,'banji_lesson_id':1,'date':'2018-04-13','week':'星期五','banji_id':4,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':5,'status':1,'creator':'151****1306','remark':'开发测试','createtime':1523109969,'updatetime':1523109969,'teacher_name':'余','status_text':'未结课','un_sign':0,'un_comment':0},{'id':25,'teacher_id':1,'banji_lesson_id':14,'date':'2018-04-13','week':'星期五','banji_id':14,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':5,'status':1,'creator':'151****1306','remark':'开发测试','createtime':1523170674,'updatetime':1523170674,'teacher_name':'余','status_text':'未结课','un_sign':1,'un_comment':1},{'id':35,'teacher_id':1,'banji_lesson_id':15,'date':'2018-04-13','week':'星期五','banji_id':15,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':5,'status':1,'creator':'151****1306','remark':'开发测试','createtime':1523170711,'updatetime':1523170711,'teacher_name':'余','status_text':'未结课','un_sign':1,'un_comment':1},{'id':45,'teacher_id':1,'banji_lesson_id':16,'date':'2018-04-13','week':'星期五','banji_id':16,'begin_time':'13:30','end_time':'14:30','dec_num':1,'class_room':5,'status':1,'creator':'151****1306','remark':'开发测试','createtime':1523170758,'updatetime':1523170758,'teacher_name':'余','status_text':'未结课','un_sign':1,'un_comment':1}]}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function get_lesson()
    {
        $banji_id=$this->request->request('banji_id');
        $banji_lesson_id=$this->request->request('banji_lesson_id');
        $date=$this->request->request('date');
        $week=$this->request->request('week');
        $teacher_id=$this->request->request('teacher_id');
        $student_id=$this->request->request('student_id');
        $lesson_id=$this->request->request('lesson_id');
        $class_room=$this->request->request('class_room');
        $not_entry=$this->request->request('not_entry');
        $is_today=$this->request->request('is_today');
        $page=$this->request->request('page',1,'intval');
        $page_size=$this->request->request('page_size',20,'intval');
        $uid=$this->auth->id;
        $get_myself=$this->request->request('get_myself',0,'intval');
        $map=[];
        $map2=[];
        $map['agency_id']=$this->auth->agency_id;
        if ($banji_id){$map['banji_id']=$banji_id;}
        if ($banji_lesson_id){$map['banji_lesson_id']=$banji_lesson_id;}
        if ($date){
            if ($is_today){
                $map['date']=['eq',$date];
            }else{
                $map['date']=['egt',$date];
            }
        }
        if ($week!=''){$map['week']=$week;}
        if ($teacher_id){$map['teacher_id']=$teacher_id;}
        if ($lesson_id){$map['lesson_id']=$lesson_id;}
        if ($get_myself){
            $student_id=db('student')->where('mobile',$this->auth->mobile)->where('status',1)->column('id');
        }
        if ($student_id){
            $check=db('shedule')->where('student_id','in',$student_id)->select();
            $banji_lesson_ids=array_unique(array_column($check,'banji_lesson_id'));
            $shedule_ids=array_unique(array_column($check,'id'));
            if (!$not_entry){
                $map['banji_lesson_id']=['in',$banji_lesson_ids];
                $map2['id']=['in',$shedule_ids];
            }else{
                $map['banji_lesson_id']=['not in',$banji_lesson_ids];
                $map2['id']=['not in',$shedule_ids];
            }
        }
        if ($class_room){$map['class_room']=$class_room;}
        $map['status']=['neq',0];
        $shedule=model('Shedule');
        $shedule->where($map);
        if ($map2){
            if ($not_entry){
                $shedule->where($map2);
            }else{
                $shedule->whereOr($map2);
            }
        }

        $data=$shedule
//            ->group('date,begin_time')
                ->group('banji_lesson_id')
            ->order('date asc')->paginate($page_size,'',['page'=>$page])->jsonSerialize();
        $uid=$this->auth->id;
        foreach ($data['data'] as &$item){
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
        $this->success('查询成功',$data);
    }



    /**
     * 新增课节补课
     * @ApiMethod   (POST)
     * @ApiParams   (name="shedule_id", type="int", required=true, description="课节id")
     * @ApiParams   (name="student_ids", type="string", required=true, description="学员id，多个用英文逗号拼接")
     * @ApiParams   (name="banji_id", type="int", required=true, description="班级id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function add_user()
    {
        $shedule_id=$this->request->post('shedule_id',0,'intval');
        $student_ids=$this->request->post('student_ids','','strval');
        if (empty($student_ids)){
            $student_ids=$this->request->post('student_id','','strval');
        }
        $banji_id=$this->request->post('banji_id','','intval');
        if (empty($shedule_id) || empty($student_ids) ||empty($banji_id)){
            $this->error('参数错误');
        }
        //检测班级是否满员
        $student_ids=explode(',',trim($student_ids,','));
        $banji_max_member=model('Banji')->where('id',$banji_id)->value('max_member');
        $member_count=model('BanjiStudent')->where('banji_id',$banji_id)->group('student_id')->count();
        $check_in_banji_user=db('shedule')->where(['student_id'=>['in',$student_ids],'banji_id'=>$banji_id,'status'=>1])->group('student_id')->count();
        if ($member_count>=$banji_max_member){
            //如果有非本班级学员，则不允许加入
            if ($check_in_banji_user!=count($student_ids)){
                $this->error('该班级已满员');
            }
        }else{
            $rest_member=$banji_max_member-$member_count;
            $need_entry_count=count($student_ids)-$check_in_banji_user;
            if (($member_count+$need_entry_count)>$banji_max_member){
                $this->error('该班级只能添加'.$rest_member.'人');
            }
        }
        $shedule=model('Shedule')->where('id',$shedule_id)->field('banji_lesson_id,banji_id,date,begin_time,end_time,id')->find();
        $info=0;
        //清理已经取消课节的同学
//        BanjiStudentCancel::clear_student_cancel($shedule_id,$student_ids);
        foreach ($student_ids as $val){
            $student_info=\app\common\model\Student::get($val);
            if(empty($student_info)){
                $this->error('该学员不存在');
            }
            $data=[
                'banji_id'=>$banji_id,
                'banji_lesson_id'=>$shedule['banji_lesson_id'],
                'student_id'=>$val,
                'shedule_id'=>$shedule['id']
            ];
            if ($check=\app\common\model\BanjiStudent::get($data)){
                $data['status']=1;
                $info=\app\common\model\BanjiStudent::update($data,['id'=>$check['id']]);
            }else{
                $info=\app\common\model\Shedule::copy_shedule($val,$shedule['banji_id'],$shedule_id);
//                $data['status']=1;
//                $data['agency_id']=$this->auth->agency_id;
//                $info=\app\common\model\BanjiStudent::create($data);
            }
            //更新学员剩余课节
            if ($info){
                model('Student')->where('id',$val)->setInc('rest_lesson');
            }
        }
        if ($info){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }


    /**
     * 课节结课、取消结课
     * @ApiMethod   (POST)
     * @ApiParams   (name="shedule_id", type="int", required=true, description="课节id")
     * @ApiParams   (name="status", type="int", required=true, description="状态：1未结课，2已完结")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function finish_shedule()
    {
        $shedule_id=$this->request->post('shedule_id',0,'intval');
        $status=$this->request->post('status');
        if (empty($shedule_id)||empty($status)){$this->error('参数错误');}
        if (!in_array($status,[1,2])){$this->error('状态传值错误');}
        $info=\app\common\model\Shedule::update(['status'=>$status,'updator'=>$this->auth->id],['id'=>$shedule_id]);
        if ($info){
            $shedule_tmp=db('shedule')->field('banji_lesson_id,banji_id,begin_time,date,end_time')->where('id',$shedule_id)->find();
            $student_ids=db('shedule')->where($shedule_tmp)->distinct(true)->column('student_id');
            //操作结课记录
            if ($status==1){
                SheduleFinish::update(['status'=>1,'remark'=>'取消结课'],$shedule_tmp);
            }elseif($status==2){
                foreach ($student_ids as $v){
                    $shedule=db('Shedule')->where($shedule_tmp)->where('student_id',$v)->field('id,status,creator,updator',true)->find();
                    $shedule_id=$shedule['id'];
                    if ($check=SheduleFinish::get(['shedule_id'=>$shedule_id])){
                        SheduleFinish::update($shedule,['id'=>$check['id']],true);
                    }else{
                        $shedule['student_id']=$v;
                        $shedule['shedule_id']=$shedule['id'];
                        $shedule['creator']=$this->auth->id;
                        $shedule['status']=1;
                        $shedule['agency_id']=$this->auth->agency_id;
                        $info=SheduleFinish::create($shedule,true);
                            //更新学员剩余课节
                            if ($info){
                                model('Student')->where('id',$v)->setDec('rest_lesson');
                            }
                        }
                }

            }
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 删除课节
     * @ApiMethod   (POST)
     * @ApiParams   (name="shedule_id", type="int", required=true, description="课节id，批量删除，多个用英文逗号分隔")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function delete_shedule()
    {
        $shedule_id=$this->request->request('shedule_id',0,'strval');
        $shedule_id=explode(',',trim($shedule_id,','));
        if (empty($shedule_id)){$this->error('参数错误');}
        $shedule_tmp=db('shedule')->where('id',$shedule_id)->field('banji_id,banji_lesson_id,date,begin_time,end_time')->find();
        $info=\app\common\model\Shedule::update(['status'=>0],$shedule_tmp);
        if ($info){
            //更新学员剩余课节
            if ($info){
                $student_list=db('shedule')->where($shedule_tmp)->column('student_id');
                if ($student_list){
                    model('Student')->where('id','in',$student_list)->setDec('rest_lesson');
                }
            }
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }


    /**
     * 删除课节学员
     * @ApiMethod   (POST)
     * @ApiParams   (name="shedule_id", type="int", required=true, description="课节id")
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/11 16:21
     */
    public function cancel_student_shedule()
    {
        $shedule_id=$this->request->post('shedule_id',0,'intval');
        $student_id=$this->request->post('student_id',0,'intval');
        $rule=['shedule_id'=>'require|gt:0','student_id'=>'require|gt:0'];
        $msg=['shedule_id'=>'课节','student_id'=>'学员'];
        $validate=new \think\Validate($rule,[],$msg);
        $check=$validate->check(['student_id'=>$student_id,'shedule_id'=>$shedule_id]);
        if (!$check){
            $this->error($validate->getError());
        }else{
            $shedule_tmp=db('Shedule')->field('banji_id,banji_lesson_id,date,begin_time,end_time')->where('id',$shedule_id)->find();
            $shedule_info=db('Shedule')->where('student_id',$student_id)->where($shedule_tmp)->find();
            $data=[
                'banji_id'=>$shedule_info['banji_id'],
                'student_id'=>$student_id,
                'banji_lesson_id'=>$shedule_info['banji_lesson_id'],
                'shedule_id'=>$shedule_info['id'],
                'status'=>1
            ];
            $info=BanjiStudentCancel::create($data,true);
            if ($info){
                model('Student')->where('id',$student_id)->setDec('rest_lesson');
                \app\common\model\Shedule::update(['status'=>0,'remark'=>'已取消'],['id'=>$shedule_info['id']]);
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }
    }

    /**
     * 编辑课节
     * @ApiMethod   (POST)
     * @ApiParams   (name="shedule_id", type="int", required=true, description="课节id")
     * @ApiParams   (name="lesson_id", type="int", required=true, description="课程id")
     * @ApiParams   (name="class_room", type="int", required=false, description="教室id")
     * @ApiParams   (name="class_room", type="int", required=false, description="教室id")
     * @ApiParams   (name="teacher_id", type="int", required=false, description="教师id")
     * @ApiParams   (name="startdate", type="date", required=false, description="开课日期（传YYYY-mm-dd）")
     * @ApiParams   (name="begin_time", type="time", required=false, description="时间点（传HH:ii）")
     * @ApiParams   (name="minute", type="int", required=false, description="分钟数")
     * @ApiParams   (name="end_time", type="time", required=false, description="结束时间点（传HH:ii）")
     * @ApiParams   (name="dec_num", type="int", required=false, description="扣课节数")
     * @ApiParams   (name="remark", type="string", required=false, description="备注")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function edit()
    {
        $shedule_id=$this->request->post('shedule_id',0,'intval');
        $lesson_id=$this->request->post('lesson_id',0,'intval');
        $student_id=$this->request->post('student_id',0,'intval');
        $class_room=$this->request->post('class_room',0,'intval');
        $teacher_id=$this->request->post('teacher_id',0,'intval');
        $startdate=$this->request->post('startdate','');
        $begin_time=$this->request->post('begin_time','');
        $minute=$this->request->post('minute',0,'intval');
        $end_time=$this->request->post('end_time','');
        $dec_num=$this->request->post('dec_num',0,'intval');
        $remark=$this->request->post('remark','');

        $rule=[
            'shedule_id'=>'require|gt:0',
            'student_id'=>'require|gt:0',
            'lesson_id'=>'require|gt:0',
            'teacher_id'=>'require|gt:0',
            'startdate'=>'require',
            'begin_time'=>'require',
            'minute'=>'number|gt:0',
            'end_time'=>'require',
            'dec_num'=>'number|egt:0',
        ];
        $msg=['shedule_id'=>'课节','student_id'=>'学员','lesson_id'=>'课程','teacher_id'=>'教师','startdate'=>'开课日期',
                'begin_time'=>'上课时间','minute'=>'上课时长','end_time'=>'下课时间','dec_num'=>'扣课时数'
            ];
        $data=[
            'shedule_id'=>$shedule_id,'lesson_id'=>$lesson_id,'student_id'=>$student_id,'class_room'=>$class_room,'teacher_id'=>$teacher_id,
            'startdate'=>$startdate,'begin_time'=>$begin_time,'minute'=>$minute,'end_time'=>$end_time,'dec_num'=>$dec_num,'remark'=>$remark
        ];
        $validate=new \think\Validate($rule,[],$msg);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            $check_status=model('Shedule')->where('id',$shedule_id)->value('status');
            if ($check_status==0){$this->error('该课节已删除');}
            if ($check_status==2){$this->error('该课节已结课不能修改');}
            $shedule_info=db('shedule')->where('id',$shedule_id)->field('banji_id,banji_lesson_id,date,begin_time,end_time')->find();
            $info=\app\common\model\Shedule::update($data,$shedule_info,true);
            if ($info){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }
    }


    /**
     * 获取课节详情
     * @ApiMethod   (GET)
     * @ApiParams   (name="shedule_id", type="int", required=true, description="课节id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/10 16:51
     */
    public function get_detail()
    {
        $shedule_id=$this->request->get('shedule_id',0,'intval');
        if (empty($shedule_id)){$this->error('操作失败');}
        $data=\app\common\model\Shedule::get($shedule_id);
        if ($data){
            $data=$data->toArray();
        }else{
            $data=[];
        }
        $this->success('操作成功',$data);
    }


    /**
     * 查询日程安排时间表
     * @ApiMethod   (POST)
     * @ApiParams   (name="startdate", type="date", required=false, description="起始日期")
     * @ApiParams   (name="enddate", type="date", required=false, description="截止日期")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/12 12:23
     */
    public function get_shedule_date()
    {
        $startdate=$this->request->request('startdate');
        $enddate=$this->request->request('enddate');
        $map=[];
        if ($startdate){
            if (empty($enddate)){$enddate=date('Y-m-d',strtotime($startdate.'+1 month'));}
            $map['date']=['between',[$startdate,$enddate]];
        }
        $map['agency_id']=$this->auth->agency_id;
        $map['status']=['neq',0];
        $data=db('shedule')->where($map)->order('date asc')->group('date,begin_time')->distinct(true)->column('date');
        $this->success('查询成功',$data);
    }
}