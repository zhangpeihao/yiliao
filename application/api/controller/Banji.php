<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/3 15:50
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 班级管理
 * Class Banji
 * @package app\api\controller
 */
class Banji extends Api{

    protected $noNeedLogin=['test'];
    protected $noNeedRight='*';
    /**
     * 获取班级列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="page", type="int", required=true, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=true, description="每页数据条数")
     * @ApiParams   (name="header_uid", type="int", required=false, description="班主任id")
     * @ApiParams   (name="class_room", type="int", required=false, description="教室ID")
     * @ApiParams   (name="type", type="int", required=false, description="班级类型：1按班级，2一对一")
     * @ApiParams   (name="name", type="int", required=false, description="班级名称")
     * @ApiReturnParams   (name="name", type="string", required=true, description="班级名称")
     * @ApiReturnParams   (name="lesson_id", type="int", required=true, description="课程id")
     * @ApiReturnParams   (name="lesson", type="string", required=true, description="课程名称")
     * @ApiReturnParams   (name="max_member", type="string", required=true, description="最大学员数")
     * @ApiReturnParams   (name="header_uid", type="int", required=true, description="班主任uid")
     * @ApiReturnParams   (name="header_user", type="string", required=true, description="班主任名称")
     * @ApiReturnParams   (name="next_lesson", type="object", required=true, description="下一节课（对象）")
     * @ApiReturnParams   (name="banji_lesson", type="array", required=true, description="班课列表（数组）")
     * @ApiReturnParams   (name="member_count", type="int", required=true, description="学员数")
     * @ApiReturnParams   (name="rest_lesson_count", type="int", required=true, description="剩余课节数")
     * @ApiReturnParams   (name="history_count", type="int", required=true, description="已上课节数")
     * @ApiReturnParams   (name="is_full", type="bool", required=true, description="是否满员")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/9 15:04
     */
    public function get_list()
    {
        $lesson_status=$this->request->request('lesson_status');
        $header_uid=$this->request->request('header_uid');
        $class_room=$this->request->request('class_room');
        $type=$this->request->request('type',1);
        $name=$this->request->request('name','','strval');
        $page=$this->request->request('page',1);
        $page_size=$this->request->request('page_size',20,'intval');
        $map=[];
        if ($lesson_status){}
        if ($header_uid){$map['header_uid']=$header_uid;}
        if ($class_room){$map['class_room']=$class_room;}
        if ($type){$map['type']=$type;}
        if ($name){$map['name']=['like','%'.$name.'%'];}
        $map['status']=['gt',0];
        $map['agency_id']=$this->auth->agency_id;
        $data=model('Banji')->where($map)->order('id desc')->paginate($page_size,[],['page'=>$page])->jsonSerialize();
        $this->success('查询成功',$data);
    }

    /**
     * 新增班级
     * @ApiMethod   (POST)
     * @ApiParams   (name="type", type="int", required=true, description="建班类型：1建班课，2一对一")
     * @ApiParams   (name="name", type="string", required=true, description="班级名称")
     * @ApiParams   (name="lesson_id", type="int", required=true, description="课程id")
     * @ApiParams   (name="max_member", type="int", required=true, description="人数上限，默认30")
     * @ApiParams   (name="header_uid", type="int", required=true, description="班主任id")
     * @ApiParams   (name="remark", type="string", required=false, description="备注")
     *
     * @ApiParams   (name="student_id", type="int", required=false, description="学员id")
     * @ApiParams   (name="class_room", type="int", required=false, description="教室id")
     * @ApiParams   (name="teacher_id", type="int", required=false, description="教师id")
     * @ApiParams   (name="startdate", type="date", required=false, description="开课日期（传YYYY-mm-dd）")
     * @ApiParams   (name="begin_time", type="time", required=false, description="时间点（传HH:ii）")
     * @ApiParams   (name="minute", type="int", required=false, description="分钟数")
     * @ApiParams   (name="end_time", type="time", required=false, description="结束时间点（传HH:ii）")
     * @ApiParams   (name="dec_num", type="int", required=false, description="扣课节数")
     * @ApiParams   (name="frequency", type="int", required=false, description="重复规则：0无，1每天，2隔天，3每周，4隔周，5,自定义")
     * @ApiParams   (name="frequency_week", type="string", required=false, description="自定义星期：多个用英文逗号分隔,其中，星期日->0,星期一->1，以此类推")
     * @ApiParams   (name="lesson_count", type="int", required=false, description="课节总数")
     *
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function add()
    {
        $creator=$this->auth->id;
        $type=$this->request->post('type',1,'intval');
        $name=$this->request->post('name','','strval');
        $lesson_id=$this->request->post('lesson_id',0,'intval');
        $max_member=$this->request->post('max_member',30,'intval');
        $header_uid=$this->request->post('header_uid',0,'intval');

        $student_id=$this->request->post('student_id',0,'intval');
        $class_room=$this->request->post('class_room',0,'intval');
        $teacher_id=$this->request->post('teacher_id',0,'intval');
        $startdate=$this->request->post('startdate','');
        $begin_time=$this->request->post('begin_time','');
        $minute=$this->request->post('minute',0,'intval');
        $end_time=$this->request->post('end_time','');
        $dec_num=$this->request->post('dec_num',0,'intval');
        $frequency=$this->request->post('frequency',0,'intval');
        $frequency_week=$this->request->post('frequency_week','');
        $lesson_count=$this->request->post('lesson_count',0,'intval');
        $remark=$this->request->post('remark','');
        if ($type==1){
            $rule=[
                'name'=>'require',
                'lesson_id'=>'require|gt:0',
                'max_member'=>'require|gt:0'
            ];
            $data=[
                'type'=>1,
                'name'=>$name,'lesson_id'=>$lesson_id,
                'max_member'=>$max_member,'header_uid'=>$header_uid,
                'remark'=>$remark
            ];
        }else{
            $rule=[
                'student_id'=>'require|gt:0',
                'lesson_id'=>'require|gt:0',
                'teacher_id'=>'require|gt:0',
                'startdate'=>'require',
                'begin_time'=>'require',
                'minute'=>'number|gt:0',
                'end_time'=>'require',
                'dec_num'=>'number|egt:0',
                'frequency'=>'number|egt:0',
                'lesson_count'=>'number|gt:0'
            ];
            $data=[
                'type'=>2,
                'name'=>model('student')->where('id',$student_id)->value('username'),
                'student_id'=>$student_id,'lesson_id'=>$lesson_id,'teacher_id'=>$teacher_id,'max_member'=>1,
                'startdate'=>$startdate,'begin_time'=>$begin_time,'minute'=>$minute,'class_room'=>$class_room,
                'end_time'=>$end_time,'dec_num'=>$dec_num,'frequency'=>$frequency,'frequency_week'=>$frequency_week,
                'lesson_count'=>$lesson_count,'remark'=>$remark,'header_uid'=>$teacher_id
            ];
        }
        $msg=['type'=>'类型','name'=>'班级名称','lesson_id'=>'课程','max_member'=>'最大成员数','header_uid'=>'班主任',
                'student_id'=>'学员','class_room'=>'教室','teacher_id'=>'教师','startdate'=>'开始日期','begin_time'=>'上课时间',
                'minute'=>'时长','end_time'=>'下课时间','dec_num'=>'扣除课时','frequency'=>'重复规则','frequency_week'=>'自定义星期',
                'lesson_count'=>'课程总数'
            ];
        $validate=new \think\Validate($rule,[],$msg);
        $res=$validate->check($data);
        if (!$res){
            $this->error($validate->getError());
        }else{
            $data['status']=1;
            $data['creator']=$creator;
            $data['agency_id']=$this->auth->agency_id;
            $info=\app\common\model\Banji::create($data,true);
            if ($info){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }
    }


    /**
     * 编辑班级信息
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="班级id")
     * @ApiParams   (name="name", type="string", required=true, description="班级名称")
     * @ApiParams   (name="lesson_id", type="int", required=true, description="课程id")
     * @ApiParams   (name="max_member", type="int", required=true, description="人数上限，默认30")
     * @ApiParams   (name="header_uid", type="int", required=true, description="班主任id")
     * @ApiParams   (name="remark", type="string", required=false, description="备注")

     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/11 14:39
     */
    public function edit()
    {
        $id=$this->request->post('id',0,'intval');
        $name=$this->request->post('name','','strval');
        $lesson_id=$this->request->post('lesson_id',0,'intval');
        $max_member=$this->request->post('max_member',30,'intval');
        $header_uid=$this->request->post('header_uid',0,'intval');
        $remark=$this->request->post('remark','');

        $rule=[
            'id'=>'require|gt:0',
            'name'=>'require',
            'lesson_id'=>'require|gt:0',
            'max_member'=>'require|gt:0'
        ];
        $msg=['id'=>'班级','name'=>'班级名称','lesson_id'=>'课程','max_member'=>'最大学员人数'];
        $data=[
            'id'=>$id,'name'=>$name,'lesson_id'=>$lesson_id,'max_member'=>$max_member,
            'header_uid'=>$header_uid,'remark'=>$remark,
            'updator'=>$this->auth->id
        ];
        $validate=new \think\Validate($rule,[],$msg);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            $info=\app\common\model\Banji::update($data,['id'=>$id]);
            if ($info){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }
    }


    /**
     * 获取班级详情
     * @ApiMethod   (GET)
     * @ApiParams   (name="banji_id", type="int", required=true, description="班级id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/9 15:37
     */
    public function get_detail()
    {
        $banji_id=$this->request->get('banji_id',0,'intval');
        if (empty($banji_id)){$this->error('参数错误');}
        $data=model('Banji')->where('id',$banji_id)->find();
        if ($data){
            $data=$data->toArray();
        }else{
            $data=[];
        }
        $this->success('查询成功',$data);
    }


    /**
     * 删除班级
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="班级id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function delete()
    {
        $id=$this->request->post('id',0,'intval');
        if (!$id){$this->error('参数错误');}
        $check=\app\common\model\Banji::get($id);
        if (empty($check)){
            $this->error('班级不存在');
        }
        if ($check['agency_id']!=$this->auth->agency_id){
            $this->error('无权限操作');
        }
        $info=\app\common\model\Banji::update(['status'=>0],['id'=>$id]);
        if ($info){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }


    /**
     * 班级完结
     * @ApiMethod   (POST)
     * @ApiParams   (name="banji_id", type="int", required=true, description="班级id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/9 15:43
     */
    public function finish_banji()
    {
        $banj_id=$this->request->post('banji_id',0,'intval');
        if (empty($banj_id)){$this->error('参数错误');}
        $info=\app\common\model\Banji::update(['status'=>2],['id'=>$banj_id]);
        if ($info){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
}