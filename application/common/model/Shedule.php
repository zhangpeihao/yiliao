<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 17:33
 */
namespace app\common\model;

use app\common\library\Auth;
use think\Db;
use think\Model;

class Shedule extends Model{
    protected $name='shedule';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=[
        'teacher_name','status_text','dispatch','lesson','banji_name','class_room_id','minute',
        'creator_text','banji_type','lesson_info', 'student_list','banji_lesson_info'
    ];

    protected static function init()
    {
        Shedule::event('before_insert',function ($shedule){
            $auth=Auth::instance();
            $shedule->agency_id=$auth->agency_id;
            $begin_time=format_string_time($shedule['begin_time']);
            $end_time=format_string_time($shedule['end_time']);
            $check=\db('shedule')->where([
                'teacher_id'=>$shedule['teacher_id'],'date'=>$shedule['date'],'agency_id'=>['neq',$shedule['agency_id']]
            ])->where(function ($query)use($shedule,$begin_time,$end_time){
                $query->whereOr(['begin_time'=>['between',[$begin_time,$end_time]]])
                    ->whereOr(['end_time'=>['between',[$begin_time,$end_time]]]);
            })->select();

            if (!empty($check)){
                return false;
            }
            $check2=\db('shedule')->where([
                'student_id'=>$shedule['student_id'],'date'=>$shedule['date']
            ])->where(function ($query)use($shedule,$begin_time,$end_time){
                $query->whereOr(['begin_time'=>['between',[$begin_time,$end_time]]])
                    ->whereOr(['end_time'=>['between',[$begin_time,$end_time]]]);
            })->select();

            if (!empty($check2)){
                return false;
            }
        });
    }

    public function getBanjiLessonInfoAttr($value,$data)
    {
        if (!empty($data['banji_lesson_id'])){
            return db('banji_lesson')->find($data['banji_lesson_id']);
        }else{
            return [];
        }
    }

    public function getStudentListAttr($value,$data)
    {
        $student_ids=db('shedule')->where('status','neq',0)
            ->where(['banji_lesson_id'=>$data['banji_lesson_id'],'date'=>$data['date'],'begin_time'=>$data['begin_time']])
            ->column('student_id','id');
        $res=[];
        $sign_list=[0 => '未确认',1=>'已到达',2=>'请假',3=>'迟到',4=>'早退',5=>'旷课'];
        foreach ($student_ids as $k=>$v){
            $student_info=\model('student')->where('id',$v)->field('id,agency_id,username,mobile,avatar,gender')->find();
            if (empty($student_info)){
                continue;
            }
            $count=\db('shedule')->where(['student_id'=>$v,'banji_lesson_id'=>$data['banji_lesson_id']])
                ->where('status','neq',0)
                ->count();
            $already=\db('shedule')
                    ->where(['student_id'=>$v,'banji_lesson_id'=>$data['banji_lesson_id']])
                    ->where('status','eq',2)
                    ->count();
            $student_info['rest_lesson']=$count-$already;
            $student_info['shedule_id']=$k;
            $shedule_info=\db('shedule')->where('id',$k)->field('sign_status,comment_status')->find();
            $sign_status=intval($shedule_info['sign_status']);
            $comment_status=intval($shedule_info['comment_status']);
            $shedule_info['sign_status_text']=$sign_list[$sign_status];
            if ($comment_status==0){
                $shedule_info['comment_status_text']='未课评';
            }else{
                $shedule_info['comment_status_text']='已课评';
            }
            $student_info['shedule_info']=$shedule_info;
            $res[]=$student_info;
        }
        return $res;
    }

    public function setBeginTimeAttr($value,$data)
    {
        return format_string_time($value);
    }

    public function setEndTimeAttr($value,$data)
    {
        return format_string_time($value);
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

    public function getBanjiTypeAttr($value,$data)
    {
        return (int)db('banji')->where('id',$data['banji_id'])->value('type');
    }

    public function getLessonAttr($value,$data)
    {
        return (string)model('Lesson')->where('id',$data['lesson_id'])->value('name');
    }

    public function getStatusTextAttr($value,$data)
    {
        $value=$value?$value:$data['status'];
        $list=[0=>'禁用',1=>'未结课',2=>'已结课',3=>'已调课'];
        return $list[$value];
    }

    public function getBanjiNameAttr($value,$data)
    {
        return (string)model('Banji')->where('id',$data['banji_id'])->value('name');
    }

    public function getDispatchAttr($value,$data)
    {
        $value=$value?$value:$data['dispatch_id'];
        if ($value==0){
            return (object)[];
        }else{
            return SheduleDispatch::get($value);
        }
    }


    public function getWeekAttr($value,$data)
    {
        $value=$value?$value:$data['week'];
        $list=[0=>'星期日',1=>'星期一',2=>'星期二',3=>'星期三',4=>'星期四',5=>'星期五',6=>'星期六'];
        return $list[$value];
    }

    public function getTeacherNameAttr($value,$data)
    {
        $value=$value?$value:$data['teacher_id'];
        return (string)model('Teacher')->where('id',$value)->value('username');
    }

    public function getBeginTimeAttr($value,$data)
    {
        $value=$value?$value:$data['begin_time'];
        return string_to_time($value);
    }

    public function getEndTimeAttr($value,$data)
    {
        $value=$value?$value:$data['end_time'];
        return string_to_time($value);
    }

    public function getClassRoomAttr($value,$data)
    {
        $value=$value?$value:$data['class_room'];
        return (string)model('ClassRoom')->where('id',$value)->value('name');
    }
    
    public function getDecNumAttr($value,$data)
    {
        return floatval($value);
    }

    public function getCreatorAttr($value,$data)
    {
        $value=$value?$value:$data['creator'];
        return model('User')->where('id',$value)->value('username');
    }


    public function getClassRoomIdAttr($value,$data)
    {
        return $data['class_room'];
    }

    public function getMinuteAttr($value,$data)
    {
        return (int)db('Banji_lesson')->where('id',$data['banji_lesson_id'])->value('minute');
    }

    public static function copy_shedule($student_id,$banji_id,$shedule_id)
    {
        if ($shedule_id){
            $shedule=db('Shedule')->where('id',$shedule_id)->field('createtime,updatetime,id',true)->find();
            if (empty($shedule)){
                return false;
            }
            $shedule['student_id']=$student_id;
            Shedule::create($shedule,true);
            return BanjiStudent::create([
                                    'agency_id'=>$shedule['agency_id'],
                                    'banji_id'=>$banji_id,
                                    'student_id'=>$student_id,
                                    'banji_lesson_id'=>$shedule['banji_lesson_id'],
                                    'shedule_id'=>$shedule_id,
                                    'status'=>1
                                ]);
        }else{
                return false;
        }

    }


    //生成课程表安排记录

    /**
     * @ApiParams   (name="banji_id", type="int", required=false, description="班级id")
     * @ApiParams   (name="student_id", type="int", required=false, description="学员id")
     * @ApiParams   (name="class_room", type="int", required=false, description="教室id")
     * @ApiParams   (name="teacher_id", type="int", required=false, description="教师id")
     * @ApiParams   (name="startdate", type="date", required=false, description="开课日期（传YYYY-mm-dd）")
     * @ApiParams   (name="begin_time", type="time", required=false, description="时间点（传HH:ii）")
     * @ApiParams   (name="minute", type="int", required=false, description="分钟数")
     * @ApiParams   (name="end_time", type="time", required=false, description="结束时间点（传HH:ii）")
     * @ApiParams   (name="dec_num", type="int", required=false, description="扣课节数")
     * @ApiParams   (name="frequency", type="int", required=false, description="重复规则：0无，1每天，2隔天，3每周，4隔周，5,自定义")
     * @ApiParams   (name="frequency_week", type="string", required=false, description="自定义星期（多个用英文逗号分隔）")
     * @ApiParams   (name="lesson_count", type="int", required=false, description="课节总数")

     */
    public static function auto_shedule($data=[],$creator,$student_list)
    {
        $frequency=$data['frequency'];
        if (empty($data['lesson_count'])){
            return false;
        }
        if (empty($student_list)){
            return false;
        }
        if (is_string($student_list)){
            if (strstr($student_list,',')){
                $student_list=explode(',',$student_list);
            }else{
                $student_list=[$student_list];
            }
        }
        $num=$data['lesson_count'];
        $agency_id=db('banji_lesson')->where('id',$data['banji_lesson_id'])->value('agency_id');
        $tmp_data=[
            'agency_id'=>intval($agency_id),
            'teacher_id'=>$data['teacher_id'],
            'banji_lesson_id'=>empty($data['banji_lesson_id'])?0:$data['banji_lesson_id'],
            'lesson_id'=>$data['lesson_id'],
            'dec_num'=>$data['dec_num'],
            'banji_id'=>empty($data['banji_id'])?0:$data['banji_id'],
            'class_room'=>$data['class_room'],
            'status'=>$data['status'],'creator'=>$creator,
            'begin_time'=>format_string_time($data['begin_time']),
            'end_time'=>format_string_time($data['end_time']),
            'remark'=>$data['remark'],
            'createtime'=>time(),'updatetime'=>time()
        ];
        $save_data=[];
        $start_date=$data['startdate'];
        $info=0;
        $enddate=null;
        switch ($frequency){
            case 0:
                //单天
                $cal_data=[
                    'date'=>$data['startdate'],
                    'week'=>date('w',strtotime($start_date))
                ];
                $save_data=array_merge($cal_data,$tmp_data);
                foreach ($student_list as $value){
                    $save_data['student_id']=$value;
                    $shedule_model=new Shedule();
                    $info=$shedule_model->save($save_data);
                    if (empty($info)){
                        return false;
                    }
                }
                $enddate=$save_data['date'];
                break;
            case 1:
                //每天
                foreach ($student_list as $value){
                    for ($i=0;$i<$num;$i++){
                        $tmp_time=strtotime($start_date." + $i day");
                        $tmp_date=date('Y-m-d',$tmp_time);
                        $cal_data=[
                            'date'=>$tmp_date,
                            'week'=>date('w',$tmp_time)
                        ];
                        $cal_data['student_id']=$value;
                        $save_data[]=array_merge($cal_data,$tmp_data);
                        $enddate=$tmp_date;
                    }
                    $shedule_model=new Shedule();
                    $info=$shedule_model->saveAll($save_data,'',100);
                    if (empty($info)){
                        return false;
                    }
                    unset($save_data);
                }
                break;
            case 2:
                //隔天
                $serval_day=0;
                foreach ($student_list as $value){
                    for ($i=0;$i<$num;$i++){
                        $tmp_time=strtotime($start_date." + $serval_day day");
                        $tmp_date=date('Y-m-d',$tmp_time);
                        $cal_data=[
                            'date'=>$tmp_date,
                            'week'=>date('w',$tmp_time)
                        ];
                        $cal_data['student_id']=$value;
                        $save_data[]=array_merge($cal_data,$tmp_data);
                        $enddate=$tmp_date;
                        $serval_day+=2;
                    }
                    $shedule_model=new Shedule();
                    $info=$shedule_model->saveAll($save_data,'',100);
                    if (empty($info)){
                        return false;
                    }
                }
                break;
            case 3:
                //每周
                $serval_week=0;
                foreach ($student_list as $value){
                    for ($i=0;$i<$num;$i++){
                        $tmp_time=strtotime($start_date." + $serval_week week");
                        $tmp_date=date('Y-m-d',$tmp_time);
                        $cal_data=[
                            'date'=>$tmp_date,
                            'week'=>date('w',$tmp_time)
                        ];
                        $cal_data['student_id']=$value;
                        $save_data[]=array_merge($cal_data,$tmp_data);
                        $enddate=$tmp_date;
                        $serval_week++;
                    }
                    $shedule_model=new Shedule();
                    $info=$shedule_model->saveAll($save_data,'',100);
                    if (empty($info)){
                        return false;
                    }
                    unset($save_data);
                }
                break;
            case 4:
                //隔周
                $serval_week=0;
                foreach ($student_list as $value){
                    for ($i=0;$i<$num;$i++){
                        $tmp_time=strtotime($start_date." + $serval_week week");
                        $tmp_date=date('Y-m-d',$tmp_time);
                        $cal_data=[
                            'date'=>$tmp_date,
                            'week'=>date('w',$tmp_time)
                        ];
                        $cal_data['student_id']=$value;
                        $save_data[]=array_merge($cal_data,$tmp_data);
                        $enddate=$tmp_date;
                        $serval_week+=2;
                    }
                    $shedule_model=new Shedule();
                    $info=$shedule_model->saveAll($save_data,'',100);
                    if (empty($info)){
                        return false;
                    }
                    unset($save_data);
                }
                break;
            case 5:
                //自定义
                if (empty($data['frequency_week'])){
                    return false;
                }
                $frequency_week=explode(',',trim($data['frequency_week'],','));
                $next_day=$start_date;
                foreach ($student_list as $value){
                    for ($i=0;$i<$num;$i++){
                        for ($m=1;$m<=7;$m++){
                            $now_date=$next_day;
                            $next_day=date("Y-m-d",strtotime($now_date." +1 day"));
                            $week=date('w',strtotime($now_date));
                            if (in_array($week,$frequency_week)){
                                $cal_data=[
                                    'date'=>$now_date,
                                    'week'=>$week
                                ];
                                $cal_data['student_id']=$value;
                                $save_data[]=array_merge($cal_data,$tmp_data);
                                $enddate=$now_date;
                                break;
                            }else{
                                continue;
                            }
                        }
                    }
                    $shedule_model=new Shedule();
                    $info=$shedule_model->saveAll($save_data,'',100);
                    if (empty($info)){
                        return false;
                    }
                    unset($save_data);
                }
                break;
        }
        if ($info){
            if (!empty($data['banji_lesson_id'])){
                db('banji_lesson')->where('id',$data['banji_lesson_id'])->update(['enddate'=>$enddate]);
            }
            return true;
        }else{
            return false;
        }
    }

    public function getCreatorTextAttr($value,$data)
    {
        return db('user')->where('id',$data['creator'])->value('username');
    }


    /*
     * 批量修改学员课程
     */
    public function edit_shedule($origin_shedule,$student_list,$new_shedule)
    {
        $table_info=Db::table('information_schema.columns')->where('table_name','fa_mall_product_order')->column('COLUMN_COMMENT','COLUMN_NAME');
        if (is_string($student_list)){
            if (strstr($student_list,',')){
                $student_ids=explode(',',$student_list);
            }else{
                $student_ids=[$student_list];
            }
        }else{
            $student_ids=$student_list;
        }
        $count=0;
        $log_id=md5($this->auth->id.'-'.'-'.time());
        $shedule_modify_log=new SheduleModifyLog();
        Db::startTrans();
        $update_info=0;
        foreach ($new_shedule as $k=>$v){
            if ($v!=$origin_shedule[$k]){
                $count++;
                foreach ($student_ids as $m){
                    $last_info=\db('shedule')->where([
                        'banji_lesson_id'=>$origin_shedule['lesson_id'],
                        'date'=>$origin_shedule['date'],
                        'begin_time'=>$origin_shedule['begin_time'],
                        'end_time'=>$origin_shedule['end_time'],
                        'student_id'=>$m
                    ])->find();
                    $log_data=[
                        'log_id'=>$log_id,
                        'shedule_id'=>$last_info['id'],
                        'field'=>$k,
                        'last_value'=>$last_info[$k],
                        'modify_value'=>$v,
                        'creator'=>$this->auth->id,
                        'remark'=>$table_info[$k]
                    ];
                    $info=$shedule_modify_log->save($log_data);
                    if ($info){
                        $update_info=Shedule::save($new_shedule,['id'=>$last_info['id']]);
                    }else{
                        Db::rollback();
                        return false;
                    }
                }
            }
        }
        if ($update_info){
            Db::commit();
            return true;
        }else{
            Db::rollback();
            return false;
        }
    }
}