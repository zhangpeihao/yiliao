<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/13
 * Time: 15:23
 */
namespace app\api\controller\backend;

use app\common\controller\Api;
use app\common\model\BanjiLesson;
use app\common\model\SheduleModifyLog;
use app\common\model\SheduleVacation;
use think\Db;
use think\Validate;

/**
 * 教务端课务管理
 * Class Shedule
 * @package app\api\controller\backend
 */
class Shedule extends Api
{
    protected $noNeedLogin='';
    
    protected $noNeedRight='*';

    /**
     * 教务端创建排课
     * @ApiMethod   (POST)
     * @ApiParams   (name="class_room", type="int", required=false, description="教室id")
     * @ApiParams   (name="teacher_id", type="int", required=false, description="教师id")
     * @ApiParams   (name="lesson_id", type="int", required=false, description="课程id")
     * @ApiParams   (name="startdate", type="date", required=false, description="开课日期（传YYYY-mm-dd）")
     * @ApiParams   (name="begin_time", type="time", required=false, description="时间点（传HH:ii）")
     * @ApiParams   (name="minute", type="int", required=false, description="分钟数")
     * @ApiParams   (name="end_time", type="time", required=false, description="结束时间点（传HH:ii）")
     * @ApiParams   (name="dec_num", type="int", required=false, description="扣课节数")
     * @ApiParams   (name="frequency", type="int", required=false, description="重复规则：0无，1每天，2隔天，3每周，4隔周，5,自定义")
     * @ApiParams   (name="frequency_week", type="string", required=false, description="自定义星期（多个用英文逗号分隔）")
     * @ApiParams   (name="lesson_count", type="int", required=false, description="课节总数")
     * @ApiParams   (name="student_list", type="string", required=false, description="学员id，多个用英文逗号拼接")
     * @ApiParams   (name="remark", type="string", required=false, description="备注")
     */
    public function add_shedule()
    {
        $student_list=request()->post('student_list','','strval');
        $class_room=request()->post('class_room',0,'intval');
        $teacher_id=request()->post('teacher_id',0,'intval');
        $lesson_id=request()->post('lesson_id',0,'intval');
        $startdate=request()->post('startdate','','string');
        $enddate=request()->post('enddate','','string');
        $begin_time=request()->post('begin_time','','string');
        $end_time=request()->post('end_time',"",'string');
        $minute=request()->post('minute',0,'intval');
        $dec_num=request()->post('dec_num',1,'intval');
        $frequency=request()->post('frequency','','intval');
        $frequency_week=request()->post('frequency','','intval');
        $lesson_count=request()->post('lesson_count',0,'intval');
        $remark=request()->post('remark','','strval');
        $rule=[
            'student_list'=>'require','teacher_id'=>'require','startdate'=>'require',
            'begin_time'=>'require','end_time'=>'require','lesson_id'=>'require',
            'minute'=>'require','dec_num'=>'require','frequency'=>'require',
            'frequency_week','lesson_count'
            ];
        $msg=[
            'student_list'=>'学员列表','teacher_id'=>'教师','startdate'=>'开始日期','enddate'=>'结束日期',
            'begin_time'=>'开始时间','end_time'=>'结束时间','minute'=>'上课时长','dec_num'=>'扣除课节数',
            'frequency'=>'重复规则','frequency_week'=>'自定义星期','lesson_count'=>'课节数','lesson_id'=>'课程'
        ];
        $data=[
            'student_list'=>$student_list,
            'class_room'=>$class_room,
            'teacher_id'=>$teacher_id,
            'lesson_id'=>$lesson_id,
            'startdate'=>$startdate,
            'enddate'=>$enddate,
            'begin_time'=>$begin_time,
            'end_time'=>$end_time,
            'minute'=>$minute,
            'dec_num'=>$dec_num,
            'frequency'=>$frequency,
            'frequency_week'=>$frequency_week,
            'lesson_count'=>$lesson_count,
            'status'=>1,
            'remark'=>empty($remark)?'教务端排课':$remark
        ];
        $validate=new Validate($rule,[],$msg);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }
        //添加banji_lesson作为排课记录
        $banji_lesson_data=[
            'student_list'=>$student_list,
            'banji_id'=>0,'lesson_id'=>$lesson_id,'class_room'=>$class_room,
            'teacher_id'=>$teacher_id, 'startdate'=>$startdate,'begin_time'=>$begin_time,
            'minute'=>$minute, 'end_time'=>$end_time,'dec_num'=>$dec_num, 'frequency'=>$frequency,
            'frequency_week'=>trim($frequency_week,','),'lesson_count'=>$lesson_count,
            'remark'=>$remark,'creator'=>$this->auth->id,'status'=>1,'agency_id'=>$this->auth->agency_id
        ];
        Db::startTrans();
        $banji_lesson=new BanjiLesson();
        $info=$banji_lesson->data($banji_lesson_data)->save();
        if ($info){
            $data['banji_lesson_id']=$banji_lesson->id;
            $info=\app\common\model\Shedule::auto_shedule($data,$this->auth->id,$student_list);

            if (!$info){
                Db::rollback();
                $this->error('课程时间冲突，排课失败');
            }
            //更新剩余课节数
            model('student')->where('id','in',$student_list)->setInc('rest_lesson',$lesson_count);
        }
        if ($info){
            Db::commit();

            $this->success('排课成功');
        }else{
            Db::rollback();
            $this->error('排课失败');
        }
    }

    /**
     * 修改课务安排
     * @ApiMethod   (POST)
     * @ApiParams   (name="banji_lesson_id", type="int", required=true, description="课务记录id")
     * @ApiParams   (name="class_room", type="int", required=false, description="教室id")
     * @ApiParams   (name="teacher_id", type="int", required=false, description="教师id")
     * @ApiParams   (name="lesson_id", type="int", required=false, description="课程id")
     * @ApiParams   (name="startdate", type="date", required=false, description="开课日期（传YYYY-mm-dd）")
     * @ApiParams   (name="begin_time", type="time", required=false, description="时间点（传HH:ii）")
     * @ApiParams   (name="minute", type="int", required=false, description="分钟数")
     * @ApiParams   (name="end_time", type="time", required=false, description="结束时间点（传HH:ii）")
     * @ApiParams   (name="dec_num", type="int", required=false, description="扣课节数")
     * @ApiParams   (name="frequency", type="int", required=false, description="重复规则：0无，1每天，2隔天，3每周，4隔周，5,自定义")
     * @ApiParams   (name="frequency_week", type="string", required=false, description="自定义星期（多个用英文逗号分隔）")
     * @ApiParams   (name="lesson_count", type="int", required=false, description="课节总数")
     * @ApiParams   (name="student_list", type="string", required=false, description="学员id，多个用英文逗号拼接")
     * @ApiParams   (name="remark", type="string", required=false, description="备注")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/16 11:41
     */
    public function edit_shedule()
    {
        $banji_lesson_id=request()->post('banji_lesson_id',0,'intval');
        $student_list=request()->post('student_list','','strval');
        $class_room=request()->post('class_room',0,'intval');
        $teacher_id=request()->post('teacher_id',0,'intval');
        $lesson_id=request()->post('lesson_id',0,'intval');
        $startdate=request()->post('startdate','','string');
        $enddate=request()->post('enddate','','string');
        $begin_time=request()->post('begin_time','','string');
        $end_time=request()->post('end_time',"",'string');
        $minute=request()->post('minute',0,'intval');
        $dec_num=request()->post('dec_num',1,'intval');
        $frequency=request()->post('frequency','','intval');
        $frequency_week=request()->post('frequency','','intval');
        $lesson_count=request()->post('lesson_count',0,'intval');
        $remark=request()->post('remark','','strval');
        if (empty($banji_lesson_id)){$this->error('请指定课务安排id');}
        if (empty($student_list)){$this->error('学员列表不能为空');}
        $rule=[
            'teacher_id'=>'require','startdate'=>'require',
            'begin_time'=>'require','end_time'=>'require','lesson_id'=>'require',
            'minute'=>'require','dec_num'=>'require','frequency'=>'require','lesson_count'=>'require'
        ];
        $msg=[
            'student_list'=>'学员列表','teacher_id'=>'教师','startdate'=>'开始日期','enddate'=>'结束日期',
            'begin_time'=>'开始时间','end_time'=>'结束时间','minute'=>'上课时长','dec_num'=>'扣除课节数',
            'frequency'=>'重复规则','frequency_week'=>'自定义星期','lesson_count'=>'课节数','lesson_id'=>'课程'
        ];
        $data=[
            'class_room'=>$class_room,
            'teacher_id'=>$teacher_id,
            'lesson_id'=>$lesson_id,
            'startdate'=>$startdate,
            'enddate'=>$enddate,
            'begin_time'=>$begin_time,
            'end_time'=>$end_time,
            'minute'=>$minute,
            'dec_num'=>$dec_num,
            'frequency'=>$frequency,
            'frequency_week'=>$frequency_week,
            'lesson_count'=>$lesson_count,
            'status'=>1,
            'remark'=>empty($remark)?'教务端调课':$remark
        ];
        $validate=new Validate($rule,[],$msg);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }
        Db::startTrans();
        $info=BanjiLesson::update($data,['id'=>$banji_lesson_id]);
        if ($info){
            $res=\app\common\model\Shedule::update(['banji_lesson_id'=>$banji_lesson_id,'status'=>0,'remark'=>'教务修改']);
            if ($res){
                $info=\app\common\model\Shedule::auto_shedule($data,$this->auth->id,$student_list);
                if ($info){
                    Db::commit();
                    $this->success('修改成功');
                }else{
                    Db::rollback();
                    $this->error('修改失败');
                }
            }else{
                Db::rollback();
                $this->error('修改失败');
            }
        }else{
            Db::rollback();
            $this->error('修改失败');
        }
    }


    /**
     * 编辑课节
     * @ApiMethod   (POST)
     * @ApiParams   (name="banji_lesson_id", type="int", required=true, description="课务记录id")
     * @ApiParams   (name="student_list", type="string", required=true, description="学员id，多个用英文逗号拼接")
     * @ApiParams   (name="lesson_id", type="int", required=true, description="课程id")
     * @ApiParams   (name="class_room", type="int", required=false, description="教室id")
     * @ApiParams   (name="teacher_id", type="int", required=false, description="教师id")
     * @ApiParams   (name="origin_date", type="date", required=true, description="原始日期（传YYYY-mm-dd）")
     * @ApiParams   (name="date", type="date", required=false, description="日期（传YYYY-mm-dd）")
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
        $banji_lesson_id=request()->post('banji_lesson_id',0,'intval');
        $lesson_id=$this->request->post('lesson_id',0,'intval');
        $student_list=$this->request->post('student_list',"",'strval');
        $class_room=$this->request->post('class_room',0,'intval');
        $teacher_id=$this->request->post('teacher_id',0,'intval');
        $origin_date=$this->request->post('origin_date','');
        $date=$this->request->post('date','');
        $begin_time=$this->request->post('begin_time','');
        $minute=$this->request->post('minute',0,'intval');
        $end_time=$this->request->post('end_time','');
        $dec_num=$this->request->post('dec_num',0,'intval');
        $remark=$this->request->post('remark','');
        if (empty($student_list)){$this->error('请选择学员');}
        if (empty($origin_date)){$this->error('原始日期不能为空');}
        $rule=[
            'banji_lesson_id'=>'require|gt:0',
            'lesson_id'=>'require|gt:0',
            'teacher_id'=>'require|gt:0',
            'date'=>'require',
            'begin_time'=>'require',
            'minute'=>'number|gt:0',
            'end_time'=>'require',
            'dec_num'=>'number|egt:0',
        ];
        $msg=['banji_lesson_id'=>'课务id','lesson_id'=>'课程','teacher_id'=>'教师','origin_date'=>'原始日期',
            'date'=>'日期', 'begin_time'=>'上课时间','minute'=>'上课时长','end_time'=>'下课时间',
            'dec_num'=>'扣课时数'
        ];
        $data=[
            'banji_lesson_id'=>$banji_lesson_id,'lesson_id'=>$lesson_id,'class_room'=>$class_room,
            'teacher_id'=>$teacher_id, 'date'=>$date,'begin_time'=>$begin_time,'minute'=>$minute,
            'end_time'=>$end_time,'dec_num'=>$dec_num,'remark'=>$remark
        ];
        $validate=new \think\Validate($rule,[],$msg);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            $check_count=model('Shedule')->where('banji_lesson_id',$banji_lesson_id)->where('date',$date)->where('status',1)->count();
            if ($check_count==0){$this->error('该课节已结课或已删除不能修改');}
            $shedule_info=db('shedule')->where('banji_lesson_id',$banji_lesson_id)
                ->where('date',$origin_date)
                ->where('status',1)
                ->field('id,banji_id,banji_lesson_id,date,begin_time,end_time,status')->find();
            $shedule_id=$shedule_info['id'];
            unset($shedule_info['id']);
            Db::startTrans();
            $info=\app\common\model\Shedule::update($data,$shedule_info,true);
            $res=1;
            $old_student_list=\db('shedule')->where($shedule_info)->distinct(true)->column('student_id');
            $student_list=explode(',',$student_list);
            $new_count=count($student_list);
            $old_count=count($old_student_list);
            if ($info){
                if ($new_count>$old_count){
                    $new_student_list=array_diff($student_list,$old_student_list);
                    foreach ($new_student_list as $v){
                        $res=\app\common\model\Shedule::copy_shedule($v,0,$shedule_id);
                    }
                }elseif ($new_count<$old_count){
                    $diff_student=array_diff($old_student_list,$student_list);
                    $res=\app\common\model\Shedule::update(
                        ['status'=>0,'remark'=>'教务端修改'],
                        [
                            'banji_lesson_id'=>$shedule_info['banji_lesson_id'],
                            'date'=>$date,
                            'student_id'=>['in',$diff_student]
                        ]
                    );
                }
                if (!$res){
                    Db::rollback();
                    $this->error('更新失败');
                }
                Db::commit();
                $this->success('操作成功');
            }else{
                Db::rollback();
                $this->error('操作失败');
            }
        }
    }


    /**
     * 教务端给学员请假、取消请假
     * @ApiMethod   (POST)
     * @ApiParams   (name="shedule_id", type="int", required=true, description="课节id，注意从student_list中获取shedule_id")
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiParams   (name="is_cancel", type="int", required=true, description="是否取消请假：0否，1是")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/23 14:39
     */
    public function set_vacation()
    {
        $shedule_id=request()->post('shedule_id',0,'intval');
        $student_id=request()->post('student_id',0,'intval');
        $is_cancel=request()->post('is_cancel',0,'intval');
        if (empty($shedule_id)){$this->error('请指定课节');}
        if (empty($student_id)){$this->error('请指定学员');}
        $check=SheduleVacation::get(['shedule_id'=>$shedule_id,'student_id'=>$student_id]);
        if ($check){
            $info=0;
            if ($is_cancel){
                $info=SheduleVacation::destroy(['id'=>$check['id']]);
                $msg='取消请假';
            }else{
                $data=\db('shedule')->where('id',$shedule_id)
                        ->field('agency_id,student_id,date,banji_lesson_id,lesson_id,teacher_id')
                        ->find();
                $data['shedule_id']=$shedule_id;
                $data['status']=2;
                $data['from']=2;
                $data['creator']=$this->auth->id;

                $info=SheduleVacation::create($data,true);
                $msg='请假';
            }
            if ($info){
                //调整状态
                \app\common\model\Shedule::update(['vacation_status'=>1],['id'=>$shedule_id]);
                $this->success($msg.'成功');
            }else{
                \app\common\model\Shedule::update(['vacation_status'=>0],['id'=>$shedule_id]);
                $this->error($msg.'失败');
            }
        }
    }


}