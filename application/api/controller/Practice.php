<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/26 16:46
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\PracticeAdmire;
use app\common\model\PracticeStudentTask;

/**
 * 练习
 * Class Practice
 * @package app\api\controller
 */
class Practice extends Api{
    protected $noNeedRight='*';

    protected $noNeedLogin=['get_list','get_detail'];

    /**
     * 获取练习列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="status", type="int", required=false, description="状态：默认1当前练习，0往期")
     * @ApiParams   (name="page", type="int", required=false, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=false, description="分页大小")
     * @ApiReturnParams   (name="id", type="int", required=true, description="练习id")
     * @ApiReturnParams   (name="teacher_id", type="int", required=true, description="老师id")
     * @ApiReturnParams   (name="title", type="string", required=true, description="练习标题")
     * @ApiReturnParams   (name="summary", type="string", required=true, description="简介")
     * @ApiReturnParams   (name="cover", type="string", required=true, description="封面")
     * @ApiReturnParams   (name="video", type="string", required=true, description="视频url")
     * @ApiReturnParams   (name="audio", type="string", required=true, description="音频url")
     * @ApiReturnParams   (name="status", type="int", required=true, description="0已结束，1未开始，2进行中")
     * @ApiReturnParams   (name="status_text", type="string", required=true, description="状态转义")
     * @ApiReturnParams   (name="begin_time", type="string", required=true, description="开始时间")
     * @ApiReturnParams   (name="end_time", type="string", required=true, description="结束时间")
     * @ApiReturnParams   (name="content", type="string", required=true, description="学习内容")
     * @ApiReturnParams   (name="target", type="string", required=true, description="学习目标")
     * @ApiReturnParams   (name="read_count", type="int", required=true, description="阅读数")
     * @ApiReturnParams   (name="pcounts", type="int", required=true, description="评论数")
     * @ApiReturnParams   (name="creator", type="array", required=true, description="创建人信息，包含id,username,avatar")
     * @ApiReturnParams   (name="task_status", type="int", required=true, description="作业提交状态：0未提交，1已提交")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1524738065','data':{'total':1,'per_page':15,'current_page':1,'last_page':1,'data':[{'id':166,'teacher_id':1,'title':'爵士乐练习','type':1,'summary':'测试','cover':'http:\/\/baidu.com','audio':'0','video':'http:\/\/baidu.com','status':1,'remark':'','begin_time':'2018-04-26 18:09:51','end_time':'2018-04-26 19:09:55','content':'学习','target':'学会','read_count':1,'pcounts':0,'creator':0,'createtime':'2018-04-26 18:09','updatetime':'2018-04-26 18:09','task_status':0,'status_text':'未开始','creator_text':''}]}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/26 17:01
     */
    public function get_list()
    {
        $status=$this->request->request('status',1,'intval');
//        $student_id=$this->request->request('student_id',0,'intval');
        $student_id=db('student')->where('mobile',$this->auth->mobile)->value('id');
        $page=$this->request->request('page',1,'intval');
        $page_size=$this->request->request('page_size',20,'intval');
        $uid=$this->auth->id;
        $map=[];
        if ($status==1){
            $map['status']=['between',[1,2]];
        }elseif ($status==0){
            $map['status']=0;
        }
//        dump($student_id);
        if ($student_id){
            $check=db('practice_student')->where('student_id',$student_id)->distinct(true)->column('pid');
            $map['id']=['in',$check];
        }
        $map['agency_id']=$this->auth->agency_id;
        $data=model('Practice')->where($map)->order('id desc,status desc,begin_time asc')
            ->paginate($page_size,[],['page'=>$page])->each(function($val,$key)use ($student_id){
                $check=db('practice_student_task')->where(['student_id'=>$student_id,'pid'=>$val['id']])->find();
                if ($check){
                    $val['task_status']=1;
                }else{
                    $val['task_status']=0;
                }
                return $val;
            })->jsonSerialize();
        $this->success('查询成功',$data);
    }


    /**
     * 查询练习详情
     * @ApiMethod   (GET)
     * @ApiParams   (name="id", type="int", required=true, description="练习id")
     * @ApiReturnParams   (name="id", type="int", required=true, description="练习id")
     * @ApiReturnParams   (name="teacher_id", type="int", required=true, description="老师id")
     * @ApiReturnParams   (name="title", type="string", required=true, description="练习标题")
     * @ApiReturnParams   (name="summary", type="string", required=true, description="简介")
     * @ApiReturnParams   (name="cover", type="string", required=true, description="封面")
     * @ApiReturnParams   (name="video", type="string", required=true, description="视频url")
     * @ApiReturnParams   (name="audio", type="string", required=true, description="音频url")
     * @ApiReturnParams   (name="status", type="int", required=true, description="0已结束，1未开始，2进行中")
     * @ApiReturnParams   (name="status_text", type="string", required=true, description="状态转义")
     * @ApiReturnParams   (name="begin_time", type="string", required=true, description="开始时间")
     * @ApiReturnParams   (name="end_time", type="string", required=true, description="结束时间")
     * @ApiReturnParams   (name="content", type="string", required=true, description="学习内容")
     * @ApiReturnParams   (name="target", type="string", required=true, description="学习目标")
     * @ApiReturnParams   (name="read_count", type="int", required=true, description="阅读数")
     * @ApiReturnParams   (name="pcounts", type="int", required=true, description="评论数")
     * @ApiReturnParams   (name="creator", type="array", required=true, description="创建人信息，包含id,username,avatar")
     * @ApiReturnParams   (name="task_status", type="int", required=true, description="作业提交状态：0未提交，1已提交")
     * @ApiReturnParams   (name="share_page", type="url", required=true, description="用于前端分享渠道的url")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1526459679','data':{'id':166,'teacher_id':1,'title':'爵士乐练习新手课程','type':1,'summary':'测试','cover':'https:\/\/music.588net.com\/uploads\/20180426\/ee6f3b1f54b14ef6e9f42d3575a09057.jpg','audio':'0','video':'http:\/\/app.tianhuayun.net\/public\/uploads\/files\/20180503\/9357aecc967718bcb0c185debc73ae4f.mp4','status':1,'remark':'','begin_time':'2018-04-26 18:09:51','end_time':'2018-04-26 19:09:55','content':'学习','target':'学会','read_count':1,'pcounts':3,'creator':{'id':1,'username':'admin','avatar':'https:\/\/music.588net.com\/assets\/img\/avatar.png','url':'\/u\/1'},'createtime':'2018-04-26 18:09','updatetime':'2018-04-26 18:09','status_text':'未开始','task_status':0,'share_page':'https:\/\/music.588net.com\/api\/share\/pratice_page\/id\/166.html'}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/7 11:18
     */
    public function get_detail()
    {
        $uid=$this->auth->id;
        $id=$this->request->param('id');
//        $student_id=$this->request->request('student_id',0,'intval');
        $student_id=db('student')->where('mobile',$this->auth->mobile)->value('id');
        $map['id']=$id;
        $data=model('Practice')->where($map)->find();
        if ($data){
            $data=$data->toArray();
        }else{
            $data=[];
        }
        $check=db('practice_student_task')->where(['student_id'=>$student_id,'pid'=>$id])->find();
        if ($check){
            $data['task_status']=1;
        }else{
            $data['task_status']=0;
        }

        db('practice')->where($map)->setInc('read_count');
        $data['share_page']=request()->domain().url('api/share/practice_page',['id'=>$id]);
        $data['share_title']='“'.$data['creator']['username'].'”的这个练习很不错，一起来围观吧！';
        $data['share_summary']=$data['title'];
        $data['share_cover']=$data['cover'];
        $this->success('查询成功',$data);
    }

    /**
     * 添加点赞
     * @ApiMethod   (POST)
     * @ApiParams   (name="pid", type="int", required=true, description="练习记录id")
     * @ApiParams   (name="cid", type="int", required=false, description="被点赞评论id")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/26 17:34
     */
    public function add_admire()
    {
        $pid=$this->request->post('pid',0,'intval');
        $cid=$this->request->post('cid',0,'intval');
        $uid=$this->auth->id;
        if (empty($pid)){$this->error('请指定练习');}
        if (PracticeAdmire::is_admire($pid,$cid,$uid)){
            $info=PracticeAdmire::cancel_admire($pid,$cid,$uid);
            if ($info){
                $this->success('取消点赞成功');
            }else{
                $this->error('已经点赞过了');
            }
        }else{
            $info=PracticeAdmire::add_admire($pid,$cid,$uid);
            if ($info){
                $this->success('点赞成功');
            }else{
                $this->error('操作失败');
            }
        }
    }

    /**
     * 学员提交作业
     * @ApiMethod   (POST)
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiParams   (name="pid", type="int", required=true, description="练习id")
     * @ApiParams   (name="video", type="string", required=true, description="视频地址")
     * @ApiParams   (name="type", type="int", required=true, description="类型：1仅限老师，2大家都可以看")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/26 17:52
     */
    public function post_task()
    {
        $student_id=$this->request->post('student_id',0,'intval');
        $pid=$this->request->post('pid',0,'intval');
        $video=$this->request->post('video','','strval');
        $type=$this->request->post('type',1);
        if (empty($student_id)||empty($pid)||empty($video)){$this->error('参数错误');}
        $info=PracticeStudentTask::create([
           'pid'=>$pid,'uid'=>$this->auth->id,'student_id'=>$student_id,'video'=>$video,'status'=>1,'type'=>$type
        ]);
        if ($info){
            $this->success('提交成功');
        }else{
            $this->error('提交失败');
        }
    }
}