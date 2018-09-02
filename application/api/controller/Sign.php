<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 13:36
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\StudentSign;

/**
 * 签到管理
 * Class Sign
 * @package app\api\controller
 */
class Sign extends Api{

    protected $noNeedLogin=[];
    protected $noNeedRight='*';

    /**
     * 查询签到列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="page", type="int", required=true, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=true, description="每页数据条数")
     * @ApiParams   (name="banji_id", type="int", required=false, description="班级id")
     * @ApiParams   (name="student_id", type="int", required=false, description="学员id")
     * @ApiParams   (name="banji_lesson_id", type="int", required=false, description="班课id")
     * @ApiParams   (name="shedule_id", type="int", required=false, description="课节id")
     * @ApiParams   (name="class_room", type="int", required=false, description="房间id")
     * @ApiParams   (name="creator", type="int", required=false, description="操作人")
     * @ApiParams   (name="startdate", type="date", required=false, description="查询开始日期")
     * @ApiParams   (name="enddate", type="date", required=false, description="查询结束日期")
     * @ApiReturnParams   (name="id", type="int", required=true, description="签到记录id")
     * @ApiReturnParams   (name="banji_id", type="int", required=true, description="班级id")
     * @ApiReturnParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiReturnParams   (name="username", type="string", required=true, description="学员姓名")
     * @ApiReturnParams   (name="banji_lesson_id", type="int", required=true, description="班课id")
     * @ApiReturnParams   (name="class_room", type="int", required=true, description="教室id")
     * @ApiReturnParams   (name="status", type="int", required=true, description="状态")
     * @ApiReturnParams   (name="status_text", type="int", required=true, description="状态转义：'0' => '未确认','1'=>'已到达','2'=>'请假','3'=>'迟到','4'=>'早退','5'=>'旷课'")
     * @ApiReturnParams   (name="createtime", type="int", required=true, description="签到时间")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1523417435','data':{'total':4,'per_page':15,'current_page':1,'last_page':1,'data':[{'id':5,'banji_id':29,'student_id':3,'username':'滴滴出行','banji_lesson_id':53,'shedule_id':105,'class_room':8,'dec_lesson':0,'status':2,'creator':10,'createtime':'2018-04-11 11:22','updatetime':'2018-04-11 11:22','status_text':'请假'},{'id':4,'banji_id':29,'student_id':2,'username':'小鱼儿','banji_lesson_id':53,'shedule_id':105,'class_room':8,'dec_lesson':1,'status':1,'creator':11,'createtime':'2018-04-11 11:16','updatetime':'2018-04-11 11:25','status_text':'已到达'},{'id':3,'banji_id':29,'student_id':4,'username':'测试','banji_lesson_id':53,'shedule_id':105,'class_room':8,'dec_lesson':1,'status':1,'creator':11,'createtime':'2018-04-11 11:05','updatetime':'2018-04-11 11:05','status_text':'已到达'},{'id':2,'banji_id':14,'student_id':1,'username':'开发测试','banji_lesson_id':14,'shedule_id':24,'class_room':5,'dec_lesson':1,'status':1,'creator':11,'createtime':'2018-04-11 10:55','updatetime':'2018-04-11 10:59','status_text':'已到达'}]}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/11 11:03
     */
    public function get_list()
    {
        $banji_id=$this->request->request('banji',0,'intval');
        $student_id=$this->request->request('student_id',0,'intval');
        $banji_lesson_id=$this->request->request('baji_lesson_id',0,'intval');
        $shedule_id=$this->request->request('shedule_id',0,'intval');
        $class_room=$this->request->request('class_room',0,'intval');
        $creator=$this->request->request('creator',0,'intval');
        $startdate=$this->request->request('startdate','');
        $enddate=$this->request->request('enddate','');
        $page=$this->request->request('page',1);
        $page_size=$this->request->request('page_size',20,'intval');
        $map=[];
        $map['agency_id']=$this->auth->agency_id;
        if ($banji_id){$map['banji_id']=$banji_id;}
        if ($student_id){$map['student_id']=$student_id;}
        if ($banji_lesson_id){$map['banji_lesson_id']=$banji_lesson_id;}
        if ($shedule_id){$map['shedule_id']=$shedule_id;}
        if ($class_room){$map['class_room']=$class_room;}
        if ($creator){$map['creator']=$creator;}
        if ($startdate!=''){$map['creator']=['between',[strtotime($startdate),strtotime($enddate)]];}
        $data=model('StudentSign')->where($map)->order('id desc')->paginate($page_size,[],['page'=>$page])->jsonSerialize();
        $this->success('查询成功',$data);
    }

    /**
     * 添加学员签到
     * @ApiMethod   (POST)
     * @ApiParams   (name="shedule_id", type="int", required=true, description="课节id")
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiParams   (name="status", type="int", required=true, description="签到状态：'0' => '未确认','1'=>'已到达','2'=>'请假','3'=>'迟到','4'=>'早退','5'=>'旷课'")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/11 12:18
     */
    public function add()
    {
        $shedule_id=$this->request->post('shedule_id',0,'intval');
        $student_id=$this->request->post('student_id',0,'intval');
        $status=$this->request->post('status',0);
        $creator=$this->auth->id;

        $rule=[
                'student_id'=>'require|gt:0',
                'shedule_id'=>'require|gt:0'
        ];
        $msg=['student_id'=>'学员','shedule_id'=>'课节'];
        //'0' => '未确认','1'=>'已到达','2'=>'请假','3'=>'迟到','4'=>'早退','5'=>'旷课'
        if (in_array($status,[1,3,4])){
            $dec_lesson=1;
        }else{
            $dec_lesson=0;
        }
        $data=[
                'agency_id'=>$this->auth->agency_id,'shedule_id'=>$shedule_id,'student_id'=>$student_id,'dec_lesson'=>$dec_lesson,'creator'=>$creator,'status'=>$status
        ];
        $validate=new \think\Validate($rule,[],$msg);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            //判断是否已经结课 0=>'禁用',1=>'未接课',2=>'已结课'
            $shedule=db('Shedule')->find($shedule_id);
            if ($shedule['status']==2){
                $this->error('该课节已结课');
            }elseif ($shedule['status']==0){
                $this->error('该课节已删除');
            }else{
                //添加签到
                $data=[
                    'banji_id'=>$shedule['banji_id'],
                    'student_id'=>$student_id,
                    'username'=>model('Student')->where('id',$student_id)->value('username'),
                    'banji_lesson_id'=>$shedule['banji_lesson_id'],
                    'lesson_id'=>$shedule['lesson_id'],
                    'shedule_id'=>$shedule_id,
                    'class_room'=>$shedule['class_room'],
                    'dec_lesson'=>$dec_lesson,
                    'status'=>$status,
                    'creator'=>$creator,
                    'agency_id'=>$this->auth->agency_id
                ];
                $check_sign=StudentSign::is_sign($student_id,$shedule_id);
                if ($check_sign!=(object)[]){
                    $info=StudentSign::update($data,['id'=>$check_sign['id']],true);
                }else{
                    $info=StudentSign::create($data,true);
                }
                if ($info){
                    //扣除课时数
                    if ($dec_lesson){
                        model('student')->where('id',$student_id)->setDec('rest_lesson',$dec_lesson);
                    }
                    \app\common\model\Shedule::update(['sign_status'=>$status],['id'=>$shedule_id]);
                    $this->success('操作成功');
                }else{
                    $this->error('操作失败');
                }
            }
        }
    }

}