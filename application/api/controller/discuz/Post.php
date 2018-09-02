<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/5/4 15:33
 */
namespace app\api\controller\discuz;

use app\common\controller\Api;
use app\common\model\DisPostAdmire;
use app\common\model\TopicPostAdmire;
use util\OSS;

/**
 * 教务端社区帖子
 * Class TopicPost
 * @package app\api\controller
 */
class Post extends Api{

    protected $noNeedRight='*';

    protected $noNeedLogin=['get_list','get_detail'];

    /**
     * 获取帖子列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="uid", type="int", required=false, description="被查询用户uid")
     * @ApiParams   (name="page", type="int", required=false, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=false, description="分页大小")
     * @ApiParams   (name="get_my_student", type="int", required=false, description="获取我的学员：用于教务端，取值：0或1")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1525422775','data':{'total':1,'per_page':15,'current_page':1,'last_page':1,'data':[{'id':2,'tid':1,'uid':11,'title':'测试一篇帖子','pics':[],'is_top':0,'video':'http:\/\/hotsoon.snssdk.com\/hotsoon\/item\/video\/_playback\/?video_id=v0200cbd0000bbm13lm4tqbgnu3rgohg&line=0&app_id=1112','time':14,'atuid':'0','readcount':0,'pcounts':0,'likecount':0,'storecount':0,'repeat':0,'sourceid':0,'sort':0,'cover':'','status':1,'content':'','from':'','ctime':'2018-05-04 16:32','utime':'2018-05-04 16:32','user':{'id':11,'agency_id':1,'group_id':5,'username':'ladder','nickname':'','password':'a33ffd8f8b1662110e3b5140f9f4c198','salt':'10k7ub','email':'','mobile':'15107141306','avatar':'https:\/\/music.588net.com\/uploads\/20180409\/9284e21f0e92fcf09e42d339c0c32118.jpg','level':1,'gender':0,'birthday':'2018-04-03','bio':'','score':0,'successions':2,'maxsuccessions':3,'prevtime':1525226972,'logintime':1525332872,'loginip':'117.100.108.105','loginfailure':0,'joinip':'117.100.114.2','jointime':1522671061,'createtime':1522671061,'updatetime':1525332872,'token':'','alias':'','registrationId':'','status':'normal','verification':{'email':0,'mobile':0},'url':'\/u\/11'}}]}}")
     * @ApiReturnParams   (name="id", type="int", required=true, description="帖子id")
     * @ApiReturnParams   (name="uid", type="int", required=true, description="发帖人id")
     * @ApiReturnParams   (name="user", type="array", required=true, description="发帖人用户信息")
     * @ApiReturnParams   (name="video", type="url", required=true, description="视频链接")
     * @ApiReturnParams   (name="time", type="int", required=true, description="视频时长")
     * @ApiReturnParams   (name="atuid", type="string", required=true, description="被艾特人的uid")
     * @ApiReturnParams   (name="readcount", type="int", required=true, description="阅读数")
     * @ApiReturnParams   (name="pcounts", type="int", required=true, description="评论数")
     * @ApiReturnParams   (name="likecount", type="int", required=true, description="点赞数")
     * @ApiReturnParams   (name="cover", type="url", required=true, description="封面")
     * @ApiReturnParams   (name="ctime", type="string", required=true, description="发布时间")
     * @ApiReturnParams   (name="utime", type="string", required=true, description="更新时间")
     * @ApiReturnParams   (name="is_admire", type="int", required=true, description="是否点赞")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/4 15:36
     */
    public function get_list()
    {
        $uid=$this->request->param('uid');
        $page=$this->request->param('page',1,'intval');
        $page_size=$this->request->param('page_size',20,'intval');
        $get_my_student=$this->request->param('get_my_student',0,'intval');
        if ($uid){$map['uid']=$uid;}
        $map['status']=1;
        if ($get_my_student){
            //检测我得学员
            if (empty($this->auth)){$this->error('请先登录');}
            $student_ids=db('shedule')->where('teacher_id','eq',function ($query){
                $query->name('teacher')->where('mobile',$this->auth->mobile)->field('id');
            })->where('status',1)->distinct(true)->column('student_id');
            if ($student_ids){
                $uids=db('user')->where('group_id','eq',2)->where('mobile','in',function ($query)use($student_ids){
                    $query->name('student')->where('id','in',$student_ids)->field('mobile');
                })->column('id');
                $map['uid']=['in',$uids];
            }else{
                $map['uid']=['eq',0];
            }
        }
        $data=model('DisPost')->where($map)->order('id desc')->paginate($page_size,'',['page'=>$page])
                ->each(function ($val,$key)use ($uid){
                    if ($uid){
                        $val['is_admire']=DisPostAdmire::is_admire($val['id'],0,$uid);
                    }else{
                        $val['is_admire']=intval(session('is_admin'));
                    }
                    return $val;
                })
                ->jsonSerialize();
        $this->success('查询成功',$data);
    }


    /**
     * 帖子详情
     * @ApiMethod   (GET)
     * @ApiParams   (name="id", type="int", required=true, description="帖子id")
     * @ApiReturnParams   (name="id", type="int", required=true, description="帖子id")
     * @ApiReturnParams   (name="uid", type="int", required=true, description="发帖人id")
     * @ApiReturnParams   (name="user", type="array", required=true, description="发帖人用户信息")
     * @ApiReturnParams   (name="video", type="url", required=true, description="视频链接")
     * @ApiReturnParams   (name="time", type="int", required=true, description="视频时长")
     * @ApiReturnParams   (name="atuid", type="string", required=true, description="被艾特人的uid")
     * @ApiReturnParams   (name="readcount", type="int", required=true, description="阅读数")
     * @ApiReturnParams   (name="pcounts", type="int", required=true, description="评论数")
     * @ApiReturnParams   (name="likecount", type="int", required=true, description="点赞数")
     * @ApiReturnParams   (name="is_admin", type="int", required=true, description="是否点赞")
     * @ApiReturnParams   (name="cover", type="url", required=true, description="封面")
     * @ApiReturnParams   (name="ctime", type="string", required=true, description="发布时间")
     * @ApiReturnParams   (name="utime", type="string", required=true, description="更新时间")
     * @ApiReturnParams   (name="is_admire", type="int", required=true, description="是否点赞")
     * @ApiReturnParams   (name="share_page", type="url", required=true, description="用于前端分享渠道的url")
     * @ApiReturnParams   (name="mini_username", type="string", required=true, description="小程序原始id")
     * @ApiReturnParams   (name="mini_path", type="string", required=true, description="小程序页面路径")
     * @ApiReturnParams   (name="share_cover", type="url", required=true, description="分享时的封面")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1526459833','data':{'id':2,'tid':1,'uid':11,'type':0,'title':'测试一篇帖子','pics':[],'is_top':0,'video':'http:\/\/hotsoon.snssdk.com\/hotsoon\/item\/video\/_playback\/?video_id=v0200cbd0000bbm13lm4tqbgnu3rgohg&line=0&app_id=1112','time':14,'atuid':'0','readcount':0,'pcounts':0,'likecount':0,'storecount':0,'repeat':0,'sourceid':0,'sort':0,'cover':'https:\/\/music.588net.com\/uploads\/20180504\/thumb\/d41396454ab80c975755423e8c981b07.jpg','status':1,'content':'','from':'','ctime':'05月04号','utime':'05月04号','share_page':'https:\/\/music.588net.com\/api\/share\/topic_page\/id\/2.html','user':{'id':11,'username':'ladder','avatar':'https:\/\/music.588net.com\/uploads\/20180409\/9284e21f0e92fcf09e42d339c0c32118.jpg','nickname':'','group_id':5,'url':'\/u\/11'},'type_text':'公开'}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/4 15:42
     */
    public function get_detail()
    {
        $id=$this->request->param('id');
        $data=model('DisPost')->where('id',$id)->find();
        $data['is_admire']=0;
        if (empty($this->auth->id)){$this->auth->id=0;}
        $data['is_admire']=TopicPostAdmire::is_admire($id,0,$this->auth->id);
        $data['share_page']=request()->domain().url('api/share/discuz_page',['id'=>$id]);
        $data['share_title']='“'.$data['user']['username'].'”的这个视频很不错，一起来围观吧！';
        $data['share_summary']=$data['title'];
        $data['share_cover']=$data['cover'];
        $data['mini_username']='gh_8a9803f4534a';
        $data['mini_path']='/pages/friendMoment/friendMoment?id='.$id;
        $this->success('查询成功',$data);
    }


    /**
     * 发布帖子
     * @ApiMethod   (POST)
     * @ApiParams   (name="title", type="string", required=false, description="标题/内容")
     * @ApiParams   (name="pics", type="string", required=false, description="图片url，多个用英文逗号分隔")
     * @ApiParams   (name="video", type="string", required=false, description="视频url")
     * @ApiParams   (name="time", type="int", required=false, description="视频时长，单位：秒")
     * @ApiParams   (name="atuid", type="string", required=false, description="被艾特人的uid，多个因为逗号间隔")
     * @ApiParams   (name="content", type="string", required=false, description="详情，预留字段")
     * @ApiParams   (name="type", type="int", required=true, description="类型：1仅限老师，0大家都可以看")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/4 15:43
     */
    public function add_post()
    {
        $title=$this->request->post('title','','strval');
        $pics=$this->request->post('pics','','strval');
        $video=$this->request->post('video','','strval');
        $time=$this->request->post('time',0,'floatval');
        $atuid=$this->request->post('atuid',0,'intval');
//        $cover=$this->request->post('cover','','strval');
        $content=$this->request->post('content','','strval');
//        $from=$this->request->post('from');
        $type=$this->request->post('type',0,'intval');
        $uid=$this->auth->id;
        if (!in_array($type,[0,1])){$this->error('参数错误');}
//        if($video && empty($time)){$this->error('请指定视频时长');}
        $data=[
            'title'=>$title,'pics'=>$pics,'video'=>$video,'time'=>$time,
            'uid'=>$uid,
            'atuid'=>$atuid,'content'=>$content,'type'=>$type
        ];
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
        $info=\app\common\model\DisPost::create($data);
        if ($info){
            $this->success('发布成功');
        }else{
            $this->error('发布失败');
        }
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
        if (empty($pid)){$this->error('请指定帖子');}
        if (TopicPostAdmire::is_admire($pid,$cid,$uid)){
            $info=DisPostAdmire::cancel_admire($pid,$cid,$uid);
            if ($info){
                $this->success('取消点赞成功');
            }else{
                $this->error('已经点赞过了');
            }
        }else{
            $info=DisPostAdmire::add_admire($pid,$cid,$uid);
            if ($info){
                $this->success('点赞成功');
            }else{
                $this->error('操作失败');
            }
        }
    }


    /**
     * 删除本人发布的帖子
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="", required=true, description="")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/22 12:26
     */
    public function delete_post()
    {
        $id=$this->request->post('id',0,'intval');
        if (empty($id)){$this->error('参数错误');}
        $check=db('dis_post')->where('id',$id)->where('status',1)->find();
        if ($check){
            if ($check['uid']!=$this->auth->id){
                $this->error('无删除权限');
            }
            $info=\app\common\model\DisPost::update(['status'=>0],['id'=>$check['id']]);
            if ($info){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('该帖不存在或已删除');
        }
    }


    /**
     * 编辑帖子
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="", required=true, description="帖子id")
     * @ApiParams   (name="title", type="string", required=false, description="标题/内容")
     * @ApiParams   (name="pics", type="string", required=false, description="图片url，多个用英文逗号分隔")
     * @ApiParams   (name="video", type="string", required=false, description="视频url")
     * @ApiParams   (name="time", type="int", required=false, description="视频时长，单位：秒")
     * @ApiParams   (name="atuid", type="string", required=false, description="被艾特人的uid，多个因为逗号间隔")
     * @ApiParams   (name="content", type="string", required=false, description="详情，预留字段")
     * @ApiParams   (name="type", type="int", required=true, description="类型：1仅限老师，0大家都可以看")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/22 12:29
     */
    public function edit_post()
    {
        $id=$this->request->post('id',0,'intval');
        $title=$this->request->post('title','','strval');
        $pics=$this->request->post('pics','','strval');
        $video=$this->request->post('video','','strval');
        $time=$this->request->post('time',0,'floatval');
        $atuid=$this->request->post('atuid',0,'intval');
        $content=$this->request->post('content','','strval');
        $type=$this->request->post('type',0,'intval');
        $uid=$this->auth->id;
        if (!in_array($type,[0,1]) || empty($id)){$this->error('参数错误');}
        $check=db('dis_post')->where('id',$id)->where('status',1)->find();
        if ($check['uid']!=$this->auth->id){
            $this->error('无编辑权限');
        }
        $data=[
            'title'=>$title,'pics'=>$pics,'video'=>$video,'time'=>$time,
            'uid'=>$uid,
            'atuid'=>$atuid,'content'=>$content,'type'=>$type
        ];
        $info=\app\common\model\DisPost::update($data,['id'=>$id]);
        if ($info){
            $this->success('发布成功');
        }else{
            $this->error('发布失败');
        }
    }
}