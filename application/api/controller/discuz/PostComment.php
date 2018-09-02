<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/5/4 16:16
 */
namespace app\api\controller\discuz;

use app\common\controller\Api;
use app\common\model\DisPostAdmire;

/**
 * 教务端社区评论
 * Class DisPostComment
 * @package app\api\controller
 */
class PostComment extends Api{
    protected $noNeedRight='*';
    protected $noNeedLogin=['get_list'];
    /**
     * 获取帖子评论列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="pid", type="int", required=true, description="帖子id")
     * @ApiParams   (name="cid", type="int", required=false, description="评论id")
     * @ApiParams   (name="show_type", type="int", required=false, description="暂时类型：0默认一个层级展示，1带有回复的两个层级")
     * @ApiParams   (name="page", type="int", required=false, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=false, description="分页大小")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1525423491','data':{'total':1,'per_page':15,'current_page':1,'last_page':1,'data':[{'id':1,'pid':2,'puid':0,'bid':0,'buid':0,'uid':0,'content':'不错','pics':[],'likecount':0,'status':1,'ctime':'2018-05-04 16:17','utime':'2018-05-04 16:17','buser':[],'user':[]}]}}")
     * @ApiReturnParams   (name="id", type="int", required=true, description="评论id")
     * @ApiReturnParams   (name="pid", type="int", required=true, description="帖子id")
     * @ApiReturnParams   (name="bid", type="int", required=true, description="被评论id")
     * @ApiReturnParams   (name="buid", type="int", required=true, description="被评论人id")
     * @ApiReturnParams   (name="buser", type="array", required=true, description="被评论人用户信息")
     * @ApiReturnParams   (name="bcomment", type="array", required=true, description="被评论回复详情")
     * @ApiReturnParams   (name="content", type="string", required=true, description="评论内容")
     * @ApiReturnParams   (name="ctime", type="string", required=true, description="评论时间")
     * @ApiReturnParams   (name="likecount", type="int", required=true, description="点赞次数")
     * @ApiReturnParams   (name="pics", type="array", required=true, description="图片")
     * @ApiReturnParams   (name="user", type="array", required=true, description="评论发布者信息")
     * @ApiReturnParams   (name="utime", type="string", required=true, description="更新时间")
     * @ApiReturnParams   (name="is_admire", type="int", required=true, description="是否点赞")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/4 16:17
     */
    public function get_list()
    {
        $pid=$this->request->request('pid',0,'intval');
        $cid=$this->request->request('cid',0,'intval');
        $page=$this->request->request('page',1,'intval');
        $show_type=$this->request->request('show_type',0,'intval');
        $page_size=$this->request->request('page_size',20,'intval');
        if (empty($pid)){$this->error('参数错误');}
        $map=[];
        if ($pid){$map['pid']=$pid;}
        if ($cid){
            //二级评论
            $map['pbid']=$cid;
            $show_type=0;
        }
        if ($show_type==1){
            $map['pbid']=0;
        }
        $uid=$this->auth->id;
        $data=model('DisPostComment')->where($map)->order('id desc')->paginate($page_size,[],['page'=>$page])
            ->each(function ($val,$key)use ($uid){
                $val['is_admire']=\app\common\model\DisPostAdmire::is_admire($val['pid'],$val['id'],$uid);
                return $val;
            })
            ->jsonSerialize();
        $this->success('查询成功',$data);
    }

    /**
     * 添加评论
     * @ApiMethod   (POST)
     * @ApiParams   (name="pid", type="int", required=true, description="帖子id")
     * @ApiParams   (name="bid", type="int", required=false, description="被评论id")
     * @ApiParams   (name="buid", type="int", required=false, description="被评论人uid")
     * @ApiParams   (name="content", type="string", required=true, description="评论内容")
     * @ApiParams   (name="pics", type="string", required=false, description="图片链接，多个以逗号拼接)
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/26 17:45
     */
    public function add_comment()
    {
        $pid=$this->request->post('pid',0,'intval');
        $bid=$this->request->post('bid',0,'intval');
        $buid=$this->request->post('buid',0,'intval');
//        $pbid=$this->request->post('pbid',0,'intval');
        $content=$this->request->post('content','','strval');
        $pics=$this->request->post('pics','','strval');
        if (empty($pid)||empty($content)){$this->error('参数错误');}
        //匿名用户评论
        if (empty($this->auth->id)){
            $this->auth->id=0;
        }
        if ($bid){
            $pbid=$this->get_pbid($bid);
        }else{
            $pbid=0;
        }
        $ip=request()->ip(0);
        $res=GetIpLookup($ip);
        $locate=explode('|',$res['address']);
        $info=\app\common\model\DisPostComment::create([
            'pid'=>$pid,'pbid'=>$pbid,'bid'=>$bid,'buid'=>$buid,'content'=>$content,'pics'=>$pics,'uid'=>$this->auth->id,
            'ip'=>$ip,'locate'=>$locate[1].$locate[2]
        ]);
        if ($info){
            if ($bid==0){
                model('DisPost')->where('id',$pid)->setInc('pcounts');
            }else{
                model('DisPostComment')->where('id',$bid)->setInc('pcounts');
            }
            $this->success('添加成功');
        }else{
            $this->error('操作失败');
        }
    }

    private function get_pbid($bid,$tmp_id=0){
        $check=db('Dis_post_comment')->where('id',$bid)->find();
        if ($check['pbid']){
            return $this->get_pbid($check['bid'],$check['bid']);
        }else{
            return $bid;
        }
    }
}