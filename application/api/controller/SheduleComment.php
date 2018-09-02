<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/8 16:29
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 课评记录
 * Class SheduleComment
 * @package app\api\controller
 */
class SheduleComment extends Api{

    protected $noNeedRight='*';

    /**
     * 获取课评记录
     * @ApiMethod   (GET)
     * @ApiParams   (name="banji_id", type="int", required=false, description="班级ID")
     * @ApiParams   (name="banji_lesson_id", type="int", required=false, description="班课ID")
     * @ApiParams   (name="shedule_id", type="int", required=false, description="课节ID")
     * @ApiParams   (name="student_id", type="int", required=false, description="学员ID")
     * @ApiParams   (name="date", type="date", required=false, description="日期")
     * @ApiParams   (name="week", type="int", required=false, description="星期")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function get_list()
    {
        $page=$this->request->request('page',1,'intval');
        $page_size=$this->request->request('page_size',20,'intval');
        $banji_id=$this->request->request('banji_id',0,'intval');
        $banji_lesson_id=$this->request->request('banji_lesson_id',0,'intval');
        $shedule_id=$this->request->request('shedule_id',0,'intval');
        $student_id=$this->request->request('student_id',0,'intval');
        $date=$this->request->request('date');
        $week=$this->request->request('week');
        $map=[];
        $map['agency_id']=$this->auth->agency_id;
        if ($banji_id){$map['banji_id']=$banji_id;}
        if ($banji_lesson_id){$map['banji_lesson_id']=$banji_lesson_id;}
        if ($shedule_id){$map['shedule_id']=$shedule_id;}
        if ($student_id){$map['student_id']=$student_id;}
        if ($date){$map['date']=$date;}
        if ($week!=''){$map['week']=$week;}

        $data=model('SheduleComment')->where($map)->order('id desc')->paginate($page_size,[],['page'=>$page])->jsonSerialize();
        $this->success('操作查询',$data);
    }


    /**
     * 新增课评
     * @ApiMethod   (POST)
     * @ApiParams   (name="shedule_id", type="int", required=true, description="课节id")
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiParams   (name="content", type="string", required=true, description="评论内容")
     * @ApiParams   (name="pics", type="string", required=true, description="图片，传url路径，多个用英文逗号拼接")
     * @ApiParams   (name="video", type="string", required=true, description="视频，传url路径，多个用英文逗号拼接")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/8 16:45
     */
    public function add()
    {
        $shedule_id=$this->request->post('shedule_id');
        $student_id=$this->request->post('student_id');
        $content=$this->request->post('content');
        $pics=$this->request->post('pics','','strval');
        $video=$this->request->post('video','','strval');
        $rule=[
            'shedule_id'=>'require|gt:0',
            'student_id'=>'require|gt:0',
            'content'=>'require'
        ];
        $msg=['shedule_id'=>'课节','学员','content'=>'内容'];
        $shedule_info=db('shedule')->where('id',$shedule_id)->find();
        $data=[
            'banji_id'=>$shedule_info['banji_id'],
            'banji_lesson_id'=>$shedule_info['banji_lesson_id'],
            'week'=>$shedule_info['week'],
            'begin_time'=>$shedule_info['begin_time'],
            'end_time'=>$shedule_info['end_time'],
            'shedule_id'=>$shedule_id,
            'student_id'=>$student_id,
            'lesson_id'=>$shedule_info['lesson_id'],
            'date'=>$shedule_info['date'],
            'content'=>$content,
            'pics'=>trim($pics),
            'video'=>trim($video),
            'creator'=>$this->auth->id,
            'agency_id'=>$this->auth->agency_id
        ];
        $validate=new \think\Validate($rule,[],$msg);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            $info=\app\common\model\SheduleComment::create($data);
            if ($info){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }
    }

    /**
     * 编辑课评
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="课评id")
     * @ApiParams   (name="shedule_id", type="int", required=true, description="课节id")
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiParams   (name="content", type="string", required=true, description="评论内容")
     * @ApiParams   (name="pics", type="string", required=true, description="图片，传url路径，多个用英文逗号拼接")
     * @ApiParams   (name="video", type="string", required=true, description="视频，传url路径，多个用英文逗号拼接")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/8 16:45
     */
    public function edit()
    {
        $id=$this->request->post('id');
        $shedule_id=$this->request->post('shedule_id');
        $student_id=$this->request->post('student_id');
        $content=$this->request->post('content');
        $pics=$this->request->post('pics','','strval');
        $video=$this->request->post('video','','strval');
        $rule=[
            'id'=>'require',
            'student_id'=>'require|gt:0',
            'shedule_id'=>'require|gt:0',
            'content'=>'require'
        ];
        $msg=['id'=>'课评ID','student_id'=>'学员','shedule_id'=>'课节','content'=>'课评内容'];
        $data=['id'=>$id,'shedule_id'=>$shedule_id,'student_id'=>$student_id,'content'=>$content,'pics'=>trim($pics),'video'=>trim($video),'creator'=>$this->auth->id];
        $validate=new \think\Validate($rule,[],$msg);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            $info=\app\common\model\SheduleComment::update($data,['id'=>$id]);
            if ($info){
                $this->success('编辑成功');
            }else{
                $this->error('编辑失败');
            }
        }
    }

}