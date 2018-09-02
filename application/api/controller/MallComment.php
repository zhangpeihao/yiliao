<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/24
 * Time: 18:01
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 产品评论
 * Class MallComment
 * @package app\api\controller
 */
class MallComment extends Api{
    protected $noNeedRight='*';

    protected $noNeedLogin=['get_list'];

    /**
     * 获取商品评论列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="pid", type="int", required=true, description="商品id")
     * @ApiParams   (name="page", type="int", required=true, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=true, description="分页大小")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 18:05
     */
    public function get_list()
    {
        $pid=$this->request->request('pid');
        $page=$this->request->request('page',1,'intval');
        $page_size=$this->request->request('page_size',20,'intval');
        if (empty($pid)){$this->error('请指定商品');}
        $data=model('MallProductComment')->where('pid',$pid)->where('status',1)->order('id desc')->paginate($page_size,[],['page'=>$page])->jsonSerialize();
        $this->success('查询成功',$data);
    }


    /**
     * 添加评论
     * @ApiMethod   (POST)
     * @ApiParams   (name="pid", type="int", required=true, description="产品id")
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
        $info=\app\common\model\MallProductComment::create([
            'pid'=>$pid,'bid'=>$bid,'buid'=>$buid,'content'=>$content,'pics'=>$pics,'uid'=>$this->auth->id
        ]);
        if ($info){
            $this->success('添加成功');
        }else{
            $this->error('操作失败');
        }
    }
}