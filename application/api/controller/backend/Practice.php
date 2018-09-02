<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/5
 * Time: 14:20
 */
namespace app\api\controller\backend;

use app\common\controller\Api;
use app\common\model\PracticeStudent;
use think\Validate;
use util\OSS;

/**
 * 教务端练习管理
 * Class Practice
 * @package app\api\controller\backend
 */
class Practice extends Api
{
    protected $noNeedRight='*';

    /**
     * 教师查看课后练习列表
     * @ApiMethod   (GET)
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
     * @date: 2018/7/5 14:53
     */
    public function get_list()
    {
        $uid=$this->auth->id;
        $mobile=$this->auth->mobile;
        $teacher=db('teacher')->where('mobile',$mobile)->where('status',1)->value('id');
        $page=$this->request->request('page',1,'intval');
        $page_size=$this->request->request('page_size',20,'intval');
        $where=[];
        if (empty($teacher)){$this->error('教师身份未绑定');}
        $where['teacher_id']=$teacher;
        $where['status']=1;
        $where['agency_id']=$this->auth->agency_id;
        $data=model('Practice')->where($where)->order('id desc,status desc,begin_time asc')->paginate($page_size,'',['page'=>$page])->jsonSerialize();
        $this->success('查询成功',$data);
    }

    /**
     * 教务端发布、更新练习
     * @ApiMethod   (POST)
     * @ApiParams   (name="practice_id", type="int", required=true, description="练习id,用于更新数据，新增时可不传，或者传0")
     * @ApiParams   (name="title", type="string", required=true, description="练习名称")
     * @ApiParams   (name="video", type="url", required=true, description="视频")
     * @ApiParams   (name="content", type="string", required=true, description="练习内容")
     * @ApiParams   (name="target", type="string", required=true, description="练习目标")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/5 14:43
     */
    public function save_practice()
    {
        $pratice_id=request()->post('practice_id',0,'int');
        $title=request()->post('title','','strval');
        $cover=request()->post('cover','','strval');
        $video=request()->post('video','','strval');
        $content=request()->post('content','','strval');
        $target=request()->post('target','','strval');

        $uid=$this->auth->id;
        $mobile=$this->auth->mobile;
        $teacher_id=db('teacher')->where('mobile',$mobile)->where('status',1)->value('id');
        $rule=['title'=>'require',/*'cover'=>'require',*/'video'=>'require','content'=>'require','target'=>'require'];
        $msg=['title'=>'练习名称','cover'=>'封面','video'=>'教学视频','content'=>'内容','target'=>'教学目标','student_id'=>'学员'];
        if (empty($teacher_id)){$this->error('教师身份未绑定');}
        $data=['title'=>$title,'cover'=>$cover,'video'=>$video,'content'=>$content,'target'=>$target];
        $validate=new Validate($rule,[],$msg);
        if (!$validate->check($data)){
            $this->error($validate->getError());
        }
        $data['teacher_id']=$teacher_id;
        $data['type']=1;
        $data['status']=1;
        $data['creator']=$uid;
        if ($pratice_id){
            $info=\app\common\model\Practice::update($data,['id'=>$pratice_id]);
        }
        $info=\app\common\model\Practice::create($data);
        if ($info){
            if ($video){
                try{
                    $str='https://music.588net.com';
                    $save_name=strtr($video,[$str=>'']);
                    $path='.'.$save_name;
                    $file_type=mime_content_type($path);
                    $save_name=ltrim($save_name,'/');
                    $result=OSS::privateUpload('voyage',$save_name,$path,['ContentType'=>$file_type]);
//                dump($result);exit();
                    $data['video']=OSS::getPublicObjectURL('voyage',$save_name);
                }catch (\Exception $e){}
            }
            $this->success('保存成功');
        }else{
            $this->error('保存失败');
        }

    }

    /**
     * 发布练习给学员
     * @ApiMethod   (POST)
     * @ApiParams   (name="student_id", type="string", required=true, description="学员id，多个用英文逗号分隔")
     * @ApiParams   (name="practice_id", type="int", required=true, description="练习id")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/5 15:40
     */
    public function send_practice()
    {
        $student_id=request()->post('student_id','','strval');
        $practice_id=request()->post('practice_id',0,'intval');
        $student_id=explode(',',$student_id);
        if (empty($student_id)){$this->error('请指定学员');}
        if (empty($practice_id)){$this->error('请指定练习');}
        $uid=$this->auth->id;
        $data=[
            'pid'=>$practice_id,'status'=>1
        ];
        foreach ($student_id as $v){
            $data['student_id']=$v;
            $check=PracticeStudent::get(['pid'=>$practice_id,'student_id'=>$v]);
            if ($check){
                $student_name=db('student')->where('id',$v)->value('username');
                $this->error($student_name.'已经发过了');
            }
            $info=\app\common\model\PracticeStudent::create($data);
        }
        if ($info){
            $this->success('发布成功');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 删除指定学员作业
     * @ApiMethod   (POST)
     * @ApiParams   (name="student_id", type="string", required=true, description="学员id,多个用英文逗号拼接")
     * @ApiParams   (name="practice_id", type="int", required=true, description="练习id")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/5 15:42
     */
    public function delete_practice()
    {
        $student_id=request()->post('student_id','','strval');
        $practice_id=request()->post('practice_id','','int');
        if (empty($student_id)){$this->error('请指定学员');}
        if (empty($title)){$this->error('请指定练习名称');}
        $teacher_id=db('teacher')->where(['mobile'=>$this->auth->mobile,'status'=>1])->value('id');
        if (empty($teacher_id)){$this->error('账号未绑定教师');}
        $info=\app\common\model\PracticeStudent::destroy(['pid'=>$practice_id,'student_id'=>['in',$student_id]]);
        if ($info){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    /**
     * 查询练习已发送的学员列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="practice_id", type="int", required=true, description="练习id")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/26 20:47
     */
    public function get_practice_student()
    {
        $practice_id=request()->get('practice_id');
        if (empty($practice_id)){$this->error('请指定练习');}

        $student_ids=db('practice_student')->where('pid',$practice_id)->column('student_id');

        if (empty($student_ids)){
            $student=[];
        }else{
            $student=model('student')->where('id','in',$student_ids)->select();
        }
        $this->success('查询成功',$student);
    }
}