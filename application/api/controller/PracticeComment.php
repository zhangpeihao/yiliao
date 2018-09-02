<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/26 17:38
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 练习评论
 * Class PracticeComment
 * @package app\api\controller
 */
class PracticeComment extends Api{
    protected $noNeedRight='*';
    protected $noNeedLogin=['get_list'];

    /**
     * 获取练习评论列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="pid", type="int", required=true, description="练习id")
     * @ApiParams   (name="cid", type="int", required=true, description="评论id")
     * @ApiParams   (name="show_type", type="int", required=true, description="暂时类型：0默认一个层级展示，1带有回复的两个层级")
     * @ApiParams   (name="page", type="int", required=false, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=false, description="分页大小")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/26 17:39
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
        $data=model('PracticeComment')->where($map)->order('id desc')->paginate($page_size,[],['page'=>$page])->jsonSerialize();
        $this->success('查询成功',$data);
    }

    /**
     * 添加评论
     * @ApiMethod   (POST)
     * @ApiParams   (name="pid", type="int", required=true, description="练习id")
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
        $content=$this->request->post('content','','strval');
        $pics=$this->request->post('pics','','strval');
        if (empty($pid)||empty($content)){$this->error('参数错误');}
        if ($bid){
            $pbid=$this->get_pbid($bid);
        }else{
            $pbid=0;
        }
        $info=\app\common\model\PracticeComment::create([
            'pid'=>$pid,'pbid'=>$pbid,'bid'=>$bid,'buid'=>$buid,'content'=>$content,'pics'=>$pics,'uid'=>$this->auth->id
        ]);
        if ($info){
            if ($bid==0){
                model('PracticeComment')->where('id',$pid)->setInc('pcounts');
            }else{
                model('PracticeComment')->where('id',$bid)->setInc('pcounts');
            }
            $this->success('添加成功');
        }else{
            $this->error('操作失败');
        }
    }

    private function get_pbid($bid,$tmp_id=0){
        $check=db('practice_comment')->where('id',$bid)->find();
        if ($check['pbid']){
            return $this->get_pbid($check['bid'],$check['bid']);
        }else{
            return $bid;
        }
    }
}