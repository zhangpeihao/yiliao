<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 18:17
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\BanjiStudentCancel;
use app\common\model\StudentSign;
use think\Db;

/**
 * 班级学员管理
 * Class BanjiStudent
 * @package app\api\controller
 */
class BanjiStudent extends Api{

    protected $noNeedRight='*';

    /**
     * 获取班级学员列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="banji_id", type="int", required=false, description="班级ID")
     * @ApiParams   (name="shedule_id", type="int", required=false, description="课节id")
     * @ApiParams   (name="banji_lesson_id", type="int", required=false, description="班课id")
     * @ApiReturnParams   (name="banji_id", type="int", required=true, description="班级id")
     * @ApiReturnParams   (name="banji_lesson_id", type="int", required=true, description="班课id")
     * @ApiReturnParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiReturnParams   (name="student", type="string", required=true, description="学员姓名")
     * @ApiReturnParams   (name="is_comment", type="bool", required=true, description="是否课评0未课评1已课评")
     * @ApiReturnParams   (name="comment", type="object", required=true, description="课评内容，字段参见’课评接口'")
     * @ApiReturnParams   (name="is_sign", type="bool", required=true, description="是否签到0未签到1已签到")
     * @ApiReturnParams   (name="sign", type="object", required=true, description="签到内容，字段参见’签到接口',其中status:'0' => '未确认','1'=>'已到达','2'=>'请假','3'=>'迟到','4'=>'早退','5'=>'旷课'")
     * @ApiReturnParams   (name="finish_banji_lesson", type="int", required=true, description="已完成班级课程数，当有banji_lesson_id，或者shedule_id时会计算获得")
     * @ApiReturnParams   (name="rest_banji_lesson_shedule", type="int", required=true, description="剩余班级课程数，当有banji_lesson_id，或者shedule_id时会计算获得")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1523175799','data':[{'id':1,'banji_id':2,'banji_lesson_id':0,'student_id':1,'status':1,'createtime':1523109835,'updatetime':1523109835,'student':'开发测试'}]}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function get_list()
    {
        $banji_id=$this->request->request('banji_id',0,'intval');
        $shedule_id=$this->request->request('shedule_id',0,'intval');
        $banji_lesson_id=$this->request->request('banji_lesson_id',0,'intval');
        $map=[];
        $map['status']=1;
//        $banji_student=model('BanjiStudent');
        $shedule_student=model('Shedule');
        if ($banji_id){$map['banji_id']=$banji_id;}
        if ($banji_lesson_id){$map['banji_lesson_id']=$banji_lesson_id;}
        /*if ($shedule_id){
            $banji_lesson_id=model('Shedule')->where('id',$shedule_id)->value('banji_lesson_id');
            $map['banji_lesson_id']=$banji_lesson_id;
            $map['shedule_id']=0;
            //排除课节删除的学员
            $cancel_student=BanjiStudentCancel::getStudent_list($shedule_id);
            $banji_student->where($map)->whereOr(function ($query)use ($cancel_student,$shedule_id){
                if ($cancel_student){
                    $query->where('shedule_id',$shedule_id)->where('student_id','not in',$cancel_student);
                }else{
                    $query->where('shedule_id',$shedule_id);
                }
            });
        }else{
            $banji_student->where($map);
        }*/
        if ($shedule_id){
            $shedule_tmp=\db('shedule')->where('id',$shedule_id)->field('banji_id,banji_lesson_id,date,begin_time,end_time')->find();
            $map=array_merge($map,$shedule_tmp);
        }
        $data=$shedule_student->where($map)->order('id desc')->group('student_id')->select();
//        dump(\db('')->getLastSql());exit();
            foreach ($data as &$item){
                if ($shedule_id){
                    $sign=StudentSign::is_sign($item['student_id'],$shedule_id);
                    $item['sign']=$sign;
                    if ($sign!=(object)[]){
                        $item['is_sign']=1;
                    }else{
                        $item['is_sign']=0;
                    }
                    $comment=\app\common\model\SheduleComment::is_comment($item['student_id'],$shedule_id);
                    $item['comment']=$comment;
                    if ($comment!=(object)[]){
                        $item['is_comment']=1;
                    }else{
                        $item['is_comment']=0;
                    }
                    $banji_lesson_count=(int)db('banji_lesson')->where('id',$banji_lesson_id)->value('lesson_count');
                    $item['finish_banji_lesson']=db('shedule_finish')->where('banji_lesson_id',$banji_lesson_id)->where('status',1)->count();
                    $item['rest_banji_lesson_shedule']=$banji_lesson_count-$item['finish_banji_lesson'];
                }else{
                    $item['is_sign']=0;
                    $item['sign']=(object)[];
                    $item['is_comment']=0;
                    $item['comment']=(object)[];
                    $item['finish_banji_lesson']=0;
                    $item['rest_banji_lesson_shedule']=0;

                }
                $item['student_info']=model('student')->find($item['student_id']);
                $item['student']=$item['student_info']['username'];
            }
        $this->success('查询成功',$data);
    }

    /**
     * 获取非本班级学员列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="banji_id", type="int", required=true, description="班级id")
     * @ApiParams   (name="shedule_id", type="int", required=true, description="课节id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/9 16:17
     */
    public function get_out_list()
    {
        $banji_id=$this->request->get('banji_id');
        $shedule_id=$this->request->get('shedule_id');
        $map=[];
        if ($banji_id){$map['banji_id']=$banji_id;}
//        if ($shedule_id){
//            $map['shedule_id']=$shedule_id;
//            $cancel_student_list=BanjiStudentCancel::getStudent_list($shedule_id);
//            if ($cancel_student_list){
//                $map['student_id']=['not in',$cancel_student_list];
//            }
//        }
        $map['status']=1;
        $map['agency_id']=$this->auth->agency_id;
        if ($shedule_id){
            $shedule_tmp=\db('shedule')->where('id',$shedule_id)->field('banji_id,banji_lesson_id,date,begin_time,end_time')->find();
            $map=array_merge($map,$shedule_tmp);
        }
        $student_list=db('shedule')->where($map)->distinct('true')->column('student_id');
        $data=\app\common\model\Student::all(['status'=>1,'id'=>['not in',$student_list],'agency_id'=>$this->auth->agency_id]);
        $this->success('查询成功',$data);
    }

    /**
     * 班级新增学员
     * @ApiMethod   (POST)
     * @ApiParams   (name="student_ids", type="string", required=true, description="学员ID,多个用英文逗号 分隔")
     * @ApiParams   (name="banji_id", type="int", required=true, description="班级ID")
     * @ApiParams   (name="type", type="int", required=true, description="选课方式：1按时间，2按批次")
     * @ApiParams   (name="shedule_ids", type="string", required=true, description="按时间课节id,用英文逗号拼接")
     * @ApiParams   (name="banji_lesson_ids", type="string", required=true, description="按批次班课id,用英文逗号拼接")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/10 12:16
     */
    public function add()
    {
        $student_ids=$this->request->post('student_ids','','strval');
        $banji_id=$this->request->post('banji_id',0,'intval');
        $type=$this->request->post('type',0,'intval');
        $shedule_ids=$this->request->post('shedule_ids','','strval');
        //教务端根据订单号来分配课程
        $out_trade_no=$this->request->post('out_trade_no','','strval');
        if (empty($shedule_ids)){
            $shedule_ids=$this->request->post('shedule_id','','strval');
        }
        $banji_lesson_ids=$this->request->post('banji_lesson_id','','strval');
        if (empty($student_ids)||empty($banji_id) ||empty($type)){$this->error('参数错误');}
        if (\app\common\model\BanjiStudent::is_full($banji_id)){
            $this->error('该班级成员已满');
        }
        $info=0;
            $student_ids=array_filter(explode(',',$student_ids));
            $shedule_ids=array_filter(explode(',',$shedule_ids));
            $banji_lesson_ids=array_filter(explode(',',$banji_lesson_ids));
            Db::startTrans();
            $info1=0;
            foreach ($student_ids as $v){
                //添加班级学员
                $info1=\app\common\model\BanjiStudent::create([
                    'banji_id'=>$banji_id,'student_id'=>$v,'status'=>1,'banji_lesson_id'=>0,'agency_id'=>$this->auth->agency_id
                ]);
                if (!$info1){
                    Db::rollback();
                    $this->error("操作失败");
                }
                if ($type==1){
                    if (count($shedule_ids)){
                        foreach ($shedule_ids as $m){
                            $info2=\app\common\model\Shedule::copy_shedule($v,$banji_id,$m);
                            if (!$info2){
                                Db::rollback();
                                $this->error('课务安排失败');
                            }
                        }
                    }
                }elseif ($type==2){
                    if (count($banji_lesson_ids)>0){
                        foreach ($banji_lesson_ids as $m){
                            $shedule=db('shedule')->where('banji_lesson_id',$m)->column('id');
                            foreach ($shedule as $value){
                                \app\common\model\Shedule::copy_shedule($v,$banji_id,$value);
                            }
                            $info2=\app\common\model\BanjiLesson::copy_banji_lesson($v,$banji_id,$m,$this->auth->agency_id);
                            if (!$info2){
                                Db::rollback();
                                $this->error('合约安排失败');
                            }
                        }
                    }
                }
            }
            Db::commit();

        if ($info1){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 移除班级成员
     * @ApiMethod   (POST)
     * @ApiParams   (name="banji_id", type="int", required=true, description="班级id")
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/9 15:25
     */
    public function delete()
    {
        $banji_id=$this->request->post('banji_id');
        $student_id=$this->request->post('student_id');
        if (empty($banji_id)||empty($student_id)){$this->error('参数错误');}
        $info=\app\common\model\BanjiStudent::update(['status'=>0],['banji_id'=>$banji_id,'student_id'=>$student_id]);
        if ($info){
            \db('Shedule')->where(['banji_id'=>$banji_id,'student_id'=>$student_id,'status'=>1])->update(['status'=>0,'utime'=>time()]);
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
}