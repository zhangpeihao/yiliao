<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/6
 * Time: 17:11
 */
namespace app\api\controller\backend;

use app\common\controller\Api;

/**
 * 订单变动记录
 * Class OrderLog
 * @package app\api\controller\backend
 */
class OrderLog extends Api
{
    protected $noNeedLogin='';

    protected $noNeedRight='*';

    /**
     * 获取课程订单变动记录
     * @ApiMethod   (POST)
     * @ApiParams   (name="order_id", type="int", required=false, description="订单id")
     * @ApiParams   (name="page", type="int", required=false, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=false, description="分页大小")
     * @ApiReturnParams   (name="remark", type="string", required=true, description="修改内容")
     * @ApiReturnParams   (name="last_value", type="string", required=true, description="上一次的值")
     * @ApiReturnParams   (name="modify_value", type="string", required=true, description="修改后的值")
     * @ApiReturnParams   (name="creator_info", type="array", required=true, description="操作人信息")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/6 17:15
     */
    public function get_list()
    {

        $order_id=request()->post('order_id',0,'intval');
        $page=$this->request->request('page',1,'intval');
        $page_size=$this->request->request('page_size',20,'intval');
        $map=[];
        if ($order_id){
            $map['order_id']=$order_id;
        }else{
            $this->error('请指定订单查询');
        }
        $data=model('MallProductOrderLog')->where($map)->field('log_id,ctime,utime,creator')->group('log_id')->order('id desc')->paginate($page_size,'',['page'=>$page])->jsonSerialize();
        $this->success('查询成功',$data);
    }
}