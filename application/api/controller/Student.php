<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/2 20:22
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 学员数据
 * Class Student
 * @package app\api\controller
 */
class Student extends Api{
    protected $noNeedLogin=[];
    protected $noNeedRight = '*';

    /**
     * 新增学员信息
     * @ApiMethod   (POST)
     * @ApiParams   (name="level_id", type="int", required=true, description="学员等级id")
     * @ApiParams   (name="username", type="string", required=true, description="学员姓名")
     * @ApiParams   (name="avatar", type="url", required=true, description="头像")
     * @ApiParams   (name="mobile", type="mobile", required=true, description="联系方式")
     * @ApiParams   (name="gender", type="int", required=true, description="性别(1男2女)")
     * @ApiParams   (name="birthday", type="date", required=true, description="出生日期")
     * @ApiParams   (name="learn_status", type="string", required=true, description="学员类型(1在读，2试听，3过期)")
     * @ApiParams   (name="status", type="int", required=false, description="学员状态('1' => '未签约',2=>'未排课')")
     * @ApiParams   (name="sno", type="string", required=false, description="学员编号")
     * @ApiParams   (name="remark", type="string", required=false, description="备注")
     * @ApiReturn (data="{'code':1,'msg':'添加成功','time':'1522729376','data':{'id':7,'username':'123123','mobile':'15107141305','avatar':'https:\/\/music.588net.com\/assets\/img\/avatar.png','gender':1,'birthday':'1992-08-27','learn_status':1,'status':0,'sno':'','remark':'','creator':11,'createtime':1522729376,'updatetime':1522729376}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function add_member()
    {
        $creator=$this->auth->getUser()->id;
        $level_id=$this->request->post('level_id',0,'intval');
        $username=$this->request->post('username','','strval');
        $avatar=$this->request->post('avatar','/assets/img/avatar.png','strval');
        $mobile=$this->request->post('mobile','','trim');
        $gender=$this->request->post('gender',0);
        $birthday=$this->request->post('birthday');
        $learn_status=$this->request->post('learn_status',0);
        $status=$this->request->post('status',1);
        $sno=$this->request->post('sno','','strval');
        $remark=$this->request->post('remark','','strval');
        $mobile=strtr($mobile,[' '=>'']);
        $rule=[
            'level'=>'require',
            'username'=>'require|length:2,30',
            'mobile'=>'require',
            'gender'=>'require|number|in:1,2,0',
            'birthday'=>'require|date',
            'learn_status'=>'require'
        ];
        $validate = new \think\Validate($rule, [], [
            'level'=>'学院等级',
            'username' =>"学员姓名",'mobile'=>'学员联系方式','gender'=>'性别','birthday'=>'出生日期',
            'learn_status'=>'学员分类'
        ]);
//        if (!is_mobile($mobile)){$this->error('手机号格式不正确');}
        $data=[
            'level'=>$level_id,
            'agency_id'=>$this->auth->agency_id,
            'username'=>$username,'avatar'=>strtr($avatar,[config('api_url')=>'']),'mobile'=>$mobile,'gender'=>$gender,
            'birthday'=>$birthday,'learn_status'=>$learn_status,'status'=>$status,
            'sno'=>$sno,'remark'=>$remark,'creator'=>$creator,'agency_id'=>$this->auth->agency_id
        ];
        $result = $validate->check($data);
        if (!$result)
        {
            $this->error($validate->getError());
        }else{
               $check=model('Student')->getByMobile($mobile);
               if ($check){
                   $this->error('该学员已添加');
               }else{
                   model('Student')->save($data);
               }
               $id=model('Student')->getLastInsID();
               if ($id){
                   $this->success('添加成功',model('student')->getById($id));
               }else{
                   $this->error('添加失败'.model('Student')->getError());
               }

        }

    }

    /**
     * 查询学员列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="page", type="string", required=false, description="当前页，默认为1")
     * @ApiParams   (name="page_size", type="string", required=false, description="每页数据条数，默认20")
     * @ApiParams   (name="id", type="string", required=false, description="学员ID")
     * @ApiParams   (name="username", type="string", required=false, description="学员姓名")
     * @ApiParams   (name="bind_wechat", type="int", required=false, description="是否绑定weixin（0未绑定1已绑定）")
     * @ApiParams   (name="learn_status", type="int", required=false, description="学员类型(1在读，2试听，3过期)")
     * @ApiParams   (name="status", type="int", required=false, description="学员状态('1' => '未签约',2=>'未排课')")
     * @ApiParams   (name="lesson", type="int", required=false, description="课程ID")
     * @ApiParams   (name="lesson_limit", type="int", required=false, description="课程区间")
     * @ApiParams   (name="contract_limit", type="int", required=false, description="合约到期天数")
     * @ApiParams   (name="teacher_student", type="int", required=false, description="获取教师学员")
     * @ApiReturnParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiReturnParams   (name="student", type="string", required=true, description="学员姓名")
     * @ApiReturnParams   (name="mobile", type="string", required=true, description="手机号")
     * @ApiReturnParams   (name="avatar", type="string", required=true, description="头像")
     * @ApiReturnParams   (name="gender", type="int", required=true, description="性别：1男2女")
     * @ApiReturnParams   (name="birthday", type="date", required=true, description="生日")
     * @ApiReturnParams   (name="learn_status", type="int", required=true, description="学习状态，参见learn_status_text")
     * @ApiReturnParams   (name="learn_status_text", type="string", required=true, description="学习状态，'1' =>'在读','2'=>'试听',3=>'过期'")
     * @ApiReturnParams   (name="status", type="int", required=true, description="学员状态，参见status_text")
     * @ApiReturnParams   (name="status_text", type="string", required=true, description="学员状态，'1' => '未签约',2=>'未排课'")
     * @ApiReturnParams   (name="banji", type="array", required=true, description="学员所在班级，包含：id班级id，name班级名称")
     * @ApiReturnParams   (name="rest_shedule", type="int", required=true, description="学员剩余课节数")
     * @ApiReturnParams   (name="rest_contract", type="int", required=true, description="学员合同到期剩余天数")
     * @ApiReturnParams   (name="learn_status_2", type="int", required=true, description="试听学员数：注意是在data并级的字段")
     * @ApiReturnParams   (name="learn_status_3", type="int", required=true, description="过期学员数：注意是在data并级的字段")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1523419657','data':{'total':1,'per_page':15,'current_page':1,'last_page':1,'data':[{'id':1,'username':'开发测试','mobile':'15107141306','avatar':'https:\/\/music.588net.com\/assets\/img\/avatar.png','gender':1,'birthday':'2018-02-01','learn_status':1,'status':1,'sno':'','remark':'','creator':'admin','createtime':0,'updatetime':0,'gender_text':'男','learn_status_text':'在读','status_text':'未签约','student_id':1,'student':'开发测试','banji':[{'id':2,'name':'开发测试'},{'id':4,'name':'开发测试'},{'id':16,'name':'开发测试'},{'id':29,'name':'呀呀呀'}],'rest_shedule':40,'rest_contract':384}],'learn_status_2':0,'learn_status_3':0}}")
     * @desc :Created by ${PRODUCT_NAME}.
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function get_member()
    {
        $id=$this->request->request('id','','intval');
        $username=$this->request->request('username','','strval');
        $bind_wechat=$this->request->request('bind_wechat','','intval');
        $learn_status=$this->request->request('learn_status','','intval');
        $status=$this->request->request('status','','intval');
        $lesson=$this->request->request('lesson','','intval');
        $lesson_limit=$this->request->request('lesson_limit','','intval');
        $contract_limit=$this->request->request('contract_limit','','intval');
        $page=$this->request->request('page',1);
        $page_size=$this->request->request('page_size',20,'intval');
        $teacher_student=$this->request->request('teacher_student',0,'intval');
        $map=[];
        $map['agency_id']=$this->auth->agency_id;
        if ($id){$map['id']=$id;}
        if ($username){$map['username']=['like','%'.$username.'%'];}
//        if ($bind_wechat){}
        if ($learn_status){$map['learn_status']=$learn_status;}
/*        if ($status){
            //'1' => '未签约',2=>'未排课'
//            $map['status']=$status;
            if ($status==1){
                $contract_student=db('contract')->where('agency_id',$this->auth->agency_id)->where('status',1)->distinct(true)->column('student_id');
                $map['id']=['not in',$contract_student];
            }elseif ($status==2){
//                $banji_student=db('banji_student')->where('status',1)->distinct(true)->column('student_id');
                $banji_student=db('shedule')->distinct(true)->column('student_id');
                $map['id']=['not in',$banji_student];
            }
        }*/
        if ($lesson){
//            $banji_lesson_id=db('banji_lesson')->where('lesson_id',$lesson)->where('status',1)->column('id');
            $map['id']=['in',
            /*db('banji_student')->where('banji_lesson_id','in',$banji_lesson_id)
                ->where('status',1)->distinct(true)->column('student_id')*/
            db('shedule')->where('lesson_id',$lesson)->column('student_id')
        ];}
        if ($lesson_limit){
            $map['rest_lesson']=['elt',$lesson_limit];
        }
        if ($contract_limit){
            $contract_student=db('contract')->where('agency_id',$this->auth->agency_id)->where('enddate','elt',date('Y-m-d',strtotime('+7 day')))->distinct(true)->column('student_id');
            $map['id']=['in',$contract_student];
        }
        if ($teacher_student){
            $teacher_id=db('teacher')->where('mobile',$this->auth->mobile)->where('status',1)->value('id');
            if ($teacher_id){
                $student_ids=db('shedule')->where('teacher_id',$teacher_id)->where('status',1)->column('student_id');
                if ($student_ids){
                    $map['id']=['in',$student_ids];
                }
            }
        }
        $map['status']=1;
        $data=model('Student')->where($map)->order('id desc')->paginate($page_size,false,['page'=>$page]);
        $data = $data->jsonSerialize();
        if ($data['data']) {
            foreach ($data['data'] as &$val) {
                $val['banji'] = model('BanjiStudent')->getStudentBanji($val['id']);
                //查询剩余课时
                /*$banji_student=db('banji_student')->where(['student_id'=>$val['id'],'banji_lesson_id'=>['gt',0],'shedule_id'=>0])
                                ->whereOr(['student_id'=>$val['id'],'shedule_id'=>['gt',0]])->where('status',1)
                                ->select();
                $val['rest_shedule']=0;
                if ($banji_student){
                    foreach ($banji_student as $v){
                        if ($v['shedule_id']==0){
                            if ($v['banji_lesson_id']>0){
                                $val['rest_shedule']+=db('shedule')->where(['banji_lesson_id'=>$v['banji_lesson_id'],'status'=>1])->count();
                            }
                        }else{
                            $val['rest_shedule']+=db('shedule')->where(['id'=>$v['shedule_id'],'status'=>1])->count();
                        }
                    }
                }else{
                    $val['rest_shedule']=0;
                }*/
                $val['rest_shedule']=db('shedule')->where('student_id',$val['id'])->where('status',1)->count();
                //查询合同到期剩余天数
                $val['rest_contract']=0;
                $contract=db('contract')->where('student_id',$val['id'])->where('status',1)->value('enddate');
                if ($contract){
                    $endtime=strtotime($contract);
                    $val['rest_contract']=ceil(($endtime-time())/(3600*24));
                }
            }
        }
//     learn_status   '1' =>'在读','2'=>'试听',3=>'过期'
        $data['learn_status_2']=db('student')->where('learn_status',2)->where('status','neq',0)->count();
        $data['learn_status_3']=db('student')->where('learn_status',3)->where('status','neq',0)->count();
        $this->success('查询成功',$data);
    }


    /**
     * 获取学员详情
     * @ApiMethod   (GET)
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/9 15:26
     */
    public function get_detail()
    {
        $student_id=$this->request->get('student_id');
        $data=\app\common\model\Student::get($student_id);
        if ($data){
            $data=$data->toArray();
            $data['banji']=model('BanjiStudent')->getStudentBanji($student_id);
        }else{
            $data=[];
        }
        $this->success('查询成功',$data);
    }


    /**
     * 删除学员
     * @ApiMethod   (POST)
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function delete_student()
    {
        $student_id=$this->request->post('student_id');
        if (empty($student_id)){$this->error('参数错误');}
        $info=\app\common\model\Student::update(['status'=>0],['id'=>$student_id]);
        if ($info){
            \app\common\model\BanjiStudent::update(['status'=>0,'remark'=>'同步删除'],['id'=>$student_id]);
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 编辑学员信息
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="学员id")
     * @ApiParams   (name="level_id", type="int", required=true, description="等级id")
     * @ApiParams   (name="username", type="string", required=true, description="学员姓名")
     * @ApiParams   (name="avatar", type="url", required=true, description="头像")
     * @ApiParams   (name="mobile", type="mobile", required=true, description="联系方式")
     * @ApiParams   (name="gender", type="int", required=true, description="性别(1男2女)")
     * @ApiParams   (name="birthday", type="date", required=true, description="出生日期")
     * @ApiParams   (name="learn_status", type="string", required=true, description="学员类型(1在读，2试听，3过期)")
     * @ApiParams   (name="status", type="int", required=false, description="学员状态('1' => '未签约',2=>'未排课')")
     * @ApiParams   (name="sno", type="string", required=false, description="学员编号")
     * @ApiParams   (name="remark", type="string", required=false, description="备注")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/10 11:03
     */
    public function edit_student()
    {
        $creator=$this->auth->getUser()->id;
        $id=$this->request->post('id',0,'intval');
        $level_id=$this->request->post('level_id',0,'intval');
        $username=$this->request->post('username','','strval');
        $avatar=$this->request->post('avatar','/assets/img/avatar.png','strval');
        $mobile=$this->request->post('mobile','');
        $gender=$this->request->post('gender',0);
        $birthday=$this->request->post('birthday');
        $learn_status=$this->request->post('learn_status',0);
        $status=$this->request->post('status',1);
        $sno=$this->request->post('sno','','strval');
        $remark=$this->request->post('remark','','strval');
        $rule=[
            'id'=>'require|gt:0',
            'level'=>'require',
            'username'=>'require|length:2,30',
            'mobile'=>'require',
            'gender'=>'require|number|in:1,2,0',
            'birthday'=>'require|date',
            'learn_status'=>'require'
        ];
        $validate = new \think\Validate($rule, [], [
            'level'=>'学员等级',
            'id'=>'ID','username' =>"学员姓名",'mobile'=>'学员联系方式','gender'=>'性别','birthday'=>'出生日期',
            'learn_status'=>'学员分类'
        ]);
        $data=[
            'id'=>$id,'level'=>$level_id,
            'username'=>$username,'avatar'=>strtr($avatar,[config('api_url')=>'']),'mobile'=>$mobile,'gender'=>$gender,
            'birthday'=>$birthday,'learn_status'=>$learn_status,'status'=>$status,
            'sno'=>$sno,'remark'=>$remark,'creator'=>$creator
        ];
        $result = $validate->check($data);
        if (!$result)
        {
            $this->error($validate->getError());
        }else{
            $info=model('Student')->save($data,['id'=>$id]);
            if ($info){
                $this->success('更新成功');
            }else{
                $this->error('添加失败');
            }
        }
    }


    /**
     * 修改学员学习状态
     * @ApiMethod   (POST)
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiParams   (name="learn_status", type="int", required=true, description="学习状态：'1' =>'在读','2'=>'试听',3=>'过期'")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/10 11:22
     */
    public function change_learn_status()
    {
        $student_id=$this->request->post('student_id',0,'intval');
        $learn_status=$this->request->post('learn_status',0,'intval');
        $rule=[
            'student_id'=>'require|gt:0',
            'learn_status'=>'require|in:1,2,3'
        ];
        $data=['student_id'=>$student_id,'learn_status'=>$learn_status];
        $validate=new \think\Validate($rule,[],['student_id'=>'学员','learn_status'=>'学习状态']);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            $info=\app\common\model\Student::update($data,['id'=>$student_id],true);
            if ($info){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }
    }


    /**
     * 查询学员课程列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id,当获取自己课程时，可以不传")
     * @ApiParams   (name="get_myself", type="int", required=false, description="获取本人课程记录，传 1,或0")
     * @ApiReturnParams   (name="lesson_id", type="int", required=true, description="课程id：根据课程id，学员id，在“查询合约列表”接口查询购买记录历史")
     * @ApiReturnParams   (name="lesson_text", type="string", required=true, description="课程名称")
     * @ApiReturnParams   (name="rest_day", type="int", required=true, description="剩余天数")
     * @ApiReturnParams   (name="rest_shedule", type="int", required=true, description="剩余课程数")
     * @ApiReturnParams   (name="student_id", type="string", required=true, description="学员id")
     * @ApiReturnParams   (name="student_text", type="string", required=true, description="学员姓名")
     * @ApiReturnParams   (name="total_shedule", type="int", required=true, description="总课时数")
     * @ApiReturnParams   (name="is_contracted", type="int", required=true, description="是否签约：0否1是")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/11 18:05
     */
    public function get_student_lesson()
    {
        $student_id=$this->request->request('student_id',0,'intval');
        $get_myself=$this->request->request('get_myself',0,'intval');
        if (empty($student_id) && empty($get_myself)){$this->error('参数错误');}
        if ($get_myself){
            $student_id=db('student')->where('mobile',$this->auth->mobile)->where('status',1)->value('id');
        }

//        dump($student_id);exit();
//        $lesson=db('ContractRecord')->where('student_id','in',$student_id)->distinct(true)->column('lesson_id');
        $lesson=db('shedule')->where('student_id',$student_id)->distinct(true)->column('lesson_id');
        /*$banji_lesson=db('banji_student')->where('student_id','in',$student_id)
                        ->where('shedule_id',0)->where('status',1)->column('id');
        $shedule_id=db('banji_student')->where('student_id','in',$student_id)
                        ->where('status',1)->where('shedule_id','gt',0)
                        ->column('shedule_id');
        $cancel_id=db('banji_student_cancel')->where('student_id','in',$student_id)->column('shedule_id');
        $shedule_id=array_diff($shedule_id,$cancel_id);
        $lesson2=db('shedule')->where('banji_lesson_id','in',$banji_lesson)->where('status',1)->whereOr('id','in',$shedule_id)
                ->distinct(true)->column('lesson_id');
        $lesson=array_merge($lesson,$lesson2);*/
//        dump($lesson);exit();
        $data=[];
        $student_info=db('student')->where('id','eq',$student_id)->field('username,id,mobile,avatar,gender,learn_status,rest_lesson')->find();
        foreach ($lesson as $v){
            $lesson=db('lesson')->where('id',$v)->value('name');
            if (empty($lesson)){
                continue;
            }
//            $check=db('contract')->where('student_id','in',$student_id)->where('lesson_id',$v)->where('status',1)->sum('lesson_count');
//            $check2=db('contract')->where('student_id','in',$student_id)->where('lesson_id',$v)->where('status',1)->sum('give_lesson');
//            $all_lesson=$check+$check2;
//            $total_shedule=$all_lesson;
//            $history_count=db('SheduleFinish')->where('student_id','in',$student_id)->where('lesson_id',$v)->count();
//            $rest_shedule=floatval($all_lesson-$history_count);
//            $contract=model('contract')->where('lesson_id',$v)->where('student_id','in',$student_id)->find();
//            if ($contract){
//                $is_contracted=1;
//                $rest_day=$contract['rest_day'];
//            }else{
//                $is_contracted=0;
//                $rest_day=0;
//            }

            $next_lesson=[];
            if ($get_myself){
                $res=model('shedule')
                    ->where('status',1)
                    ->where('student_id','eq',$student_id)
                    ->where('lesson_id',$v)
                    ->order('id', 'asc')
                    ->find();
//                dump($res);
                if ($res){
                    $next_lesson=$res->toArray();
                }else{
                    continue;
                }
            }else{
                $next_lesson=(object)[];
            }

            if (is_array($student_id)){$student_id=implode(',',$student_id);}
            $total_shedule=db('shedule')->where(['student_id'=>$student_id,'lesson_id'=>$v])
                            ->where('status','egt',1)->count();
            $already_shedule=db('shedule')->where('student_id',$student_id)->where('lesson_id',$v)
                            ->where('status','gt',1)->count();
            $rest_shedule=$total_shedule-$already_shedule;
            $rest_day=db('shedule')->where('student_id',$student_id)->where('lesson_id',$v)
                        ->where('status',1)->group('date')->count();
            $data[]=[
                'lesson_id'=>$v,
                'lesson_text'=>$lesson,
                'total_shedule'=>$total_shedule,
                'rest_shedule'=>$rest_shedule,
                'student_id'=>$student_id,
                'student_text'=>$student_info['username'],
                'is_contracted'=>1,
                'rest_day'=>$rest_day,
                'next_lesson'=>$next_lesson
            ];
        }
        $this->success('查询成功',$data);
    }
}