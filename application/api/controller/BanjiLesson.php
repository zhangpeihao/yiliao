<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/8 15:32
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 班级课程
 * Class BanjiLesson
 * @package app\api\controller
 */
class BanjiLesson extends Api{

    protected $noNeedRight='*';


    /**
     * 获取班级课程批次
     * @ApiMethod   (GET)
     * @ApiParams   (name="banji_id", type="int", required=false, description="班级id")
     * @ApiParams   (name="class_room", type="int", required=false, description="教室id")
     * @ApiParams   (name="lesson_id", type="int", required=false, description="课程id")
     * @ApiParams   (name="teacher_id", type="int", required=false, description="教师id")
     * @ApiParams   (name="startdate", type="string", required=false, description="开始日期")
     * @ApiParams   (name="get_myself", type="int", required=false, description="查询本人的班课记录，传 1")
     * @ApiParams   (name="page", type="int", required=false, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=false, description="每页数据条数，默认20")
     * @ApiReturnParams   (name="id", type="int", required=true, description="班课id")
     * @ApiReturnParams   (name="banji_id", type="int", required=true, description="班级id")
     * @ApiReturnParams   (name="class_room", type="string", required=true, description="教室")
     * @ApiReturnParams   (name="lesson_id", type="int", required=true, description="课程id")
     * @ApiReturnParams   (name="lesson", type="string", required=true, description="课程名称")
     * @ApiReturnParams   (name="teacher_id", type="int", required=true, description="教师id")
     * @ApiReturnParams   (name="teacher", type="string", required=true, description="教师姓名")
     * @ApiReturnParams   (name="startdate", type="date", required=true, description="开课日期")
     * @ApiReturnParams   (name="enddate", type="string", required=true, description="结束日期")
     * @ApiReturnParams   (name="begin_time", type="time", required=true, description="上课开始时间")
     * @ApiReturnParams   (name="end_time", type="time", required=true, description="上课结束时间")
     * @ApiReturnParams   (name="minute", type="int", required=true, description="上课时长（分钟）")
     * @ApiReturnParams   (name="dec_num", type="int", required=true, description="扣课时数")
     * @ApiReturnParams   (name="frequency", type="int", required=true, description="重复规则，参见frequency_text")
     * @ApiReturnParams   (name="frequency_text", type="string", required=true, description="重复规则，包含：0=>'无',1=>'每天',2=>'隔天',3=>'每周',4=>'隔周',5=>'自定义'")
     * @ApiReturnParams   (name="frequency_week", type="string", required=true, description="自定义星期")
     * @ApiReturnParams   (name="lesson_count", type="int", required=true, description="课节总数")
     * @ApiReturnParams   (name="remark", type="string", required=true, description="备注")
     * @ApiReturn (data="{'code':1,'msg':'操作成功','time':'1523179961','data':{'total':1,'per_page':20,'current_page':1,'last_page':1,'data':[{'id':14,'banji_id':14,'class_room':'二年1班','lesson_id':1,'teacher_id':1,'startdate':'2018-04-07','begin_time':'13:30','minute':60,'end_time':'14:30','dec_num':1,'frequency':2,'frequency_week':'1,3,4','lesson_count':10,'remark':'开发测试','status':1,'creator':11,'updator':0,'createtime':1523170674,'updatetime':1523170674,'lesson':'尤克里里弹唱','teacher':'余','enddate':'2018-04-25'}]}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/9 15:06
     */
    public function get_list()
    {
        $banji_id=$this->request->request('banji_id');
        $class_room=$this->request->request('class_room');
        $lesson_id=$this->request->request('lesson_id');
        $teacher_id=$this->request->request('teacher_id');
        $startdate=$this->request->request('startdate');
        $page=$this->request->request('page',1,'intval');
        $page_size=$this->request->request('page_size',20,'intval');
        $get_myself=$this->request->request('get_myself',0,'intval');
        $map=[];
        if ($banji_id){$map['banji_id']=$banji_id;}
        if ($class_room){$map['class_room']=$class_room;}
        if ($lesson_id){$map['lesson_id']=$lesson_id;}
        if ($teacher_id){$map['teacher_id']=$teacher_id;}
//        if ($startdate){$map['startdate']=$startdate;}
        if ($startdate){$map['enddate']=['egt',$startdate];}
        if ($get_myself){
            $student_id=db('student')->where('mobile',$this->auth->mobile)->where('status',1)->column('id');
            $banji_lesson_id=db('shedule')->distinct(true)->where('student_id','in',$student_id)->column('banji_lesson_id');
            $map['id']=['in',$banji_lesson_id];
        }
        $map['agency_id']=$this->auth->agency_id;
        $map['status']=['gt',0];
        $data=model('BanjiLesson')->where($map)->order('startdate asc')->paginate($page_size,'',['page'=>$page])->jsonSerialize();

        $this->success('操作成功',$data);
    }


    /**
     * 新增班课
     * @ApiMethod   (POST)
     * @ApiParams   (name="banji_id", type="int", required=true, description="班级id")
     * @ApiParams   (name="lesson_id", type="int", required=true, description="课程id")
     * @ApiParams   (name="student_ids", type="string", required=false, description="学员id，多个用英文逗号分隔")
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
     * @ApiParams   (name="remark", type="string", required=false, description="备注")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/13 14:43
     */
    public function add()
    {
        $banji_id=$this->request->post('banji_id',0,'intval');
        $lesson_id=$this->request->post('lesson_id',0,'intval');
        $student_ids=$this->request->post('student_ids',0,'strval');
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

        $rule=[
            'banji_id'=>'require',
//            'student_ids'=>'require',
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
        $msg=['banji_id'=>'班级','student_ids'=>'学员','lesson_id'=>'课程','teacher_id'=>'老师','startdate'=>'开始日期',
                'begin_time'=>'开始时间','minute'=>'上课时长','end_time'=>'下课时间','dec_num'=>'扣课时数','frequency'=>'重复规则',
                'lesson_count'=>'课节数'
            ];
        $data=['banji_id'=>$banji_id,'lesson_id'=>$lesson_id,'student_ids'=>$student_ids,'class_room'=>$class_room,
                'teacher_id'=>$teacher_id, 'startdate'=>$startdate,'begin_time'=>$begin_time,'minute'=>$minute,
                'end_time'=>$end_time,'dec_num'=>$dec_num, 'frequency'=>$frequency,'frequency_week'=>trim($frequency_week,','),
                'lesson_count'=>$lesson_count,'remark'=>$remark,'creator'=>$this->auth->id,'status'=>1
            ];
        $validate=new \think\Validate($rule,[],$msg);$check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            $BanjiLesson=new \app\common\model\BanjiLesson();
            unset($data['student_ids']);
            $info=$BanjiLesson->save($data);
            if ($info){
                $student_ids=explode(',',trim($student_ids,','));
                foreach ($student_ids as $v){
                    \app\common\model\BanjiStudent::create([
                        'banji_id'=>$banji_id,'banji_lesson_id'=>$BanjiLesson->id,'student_id'=>$v,'status'=>1,'agency_id'=>$this->auth->agency_id
                    ]);
                }
                //更新学员剩余课程数
                model('Student')->where('id','in',$student_ids)->setInc('rest_lesson',$lesson_count);
                $this->success('操作成功');
            }else{
                $this->error('添加失败');
            }
        }
    }

}