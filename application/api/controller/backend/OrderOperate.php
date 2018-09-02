<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/6
 * Time: 17:20
 */
namespace app\api\controller\backend;

use app\common\controller\Api;
use app\common\model\MallProductOrder;
use app\common\model\MallWuliu;
use app\common\model\UserAddress;

/**
 * 教务端操作订单状态
 * Class OrderOperate
 * @package app\api\controller\backend
 */
class OrderOperate extends Api
{
    protected $noNeedRight='*';

    protected $noNeedLogin='';

    /**
     * 获取物流公司列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="", type="", required=true, description="")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/6 17:21
     */
    public function get_wuliu_list()
    {
        $page=request()->request('page',1,'intval');
        $page_size=$this->request->request('page_size',20,'intval');
        $data=db('mall_wuliu_company')->paginate($page_size,'',['page'=>$page])->jsonSerialize();
        $this->success('查询成功',$data);
    }

    /**
     * 订单发货
     * @ApiMethod   (POST)
     * @ApiParams   (name="post_code", type="string", required=true, description="物流公司编号")
     * @ApiParams   (name="post_num", type="string", required=true, description="物流单号")
     * @ApiParams   (name="order_id", type="string", required=true, description="订单id")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/6 17:24
     */
    public function send_order()
    {
        $post_code=request()->post('post_code','','strval');
        $post_num=request()->post('post_num','','strval');
        $order_id=$this->request->post('order_id','','intval');
        $order=MallProductOrder::get(['id'=>$order_id]);
        $defaut_address=UserAddress::get(['uid'=>$order['uid'],'isdefault'=>1]);
        if (empty($post_code)){$this->error('请选择快递公司');}
        if (empty($post_num)){$this->error('请填写快递编号');}
        $data=[
            'post_num'=>$post_num,
            'order_id'=>$order_id,
            'out_trade_no'=>$order['out_trade_no'],
            'uid'=>$order['uid'],
            'post_user'=>$this->auth->username,
            'post_mobile'=>$this->auth->mobile,
            'post_type'=>strtolower($post_code),
            'address'=>$defaut_address['address'],
            'accept_user'=>$defaut_address['username'],
            'accept_mobile'=>$defaut_address['mobile'],
            'status'=>1
        ];
        $info=MallWuliu::create($data);
        if ($info){
            MallProductOrder::update(['post_num'=>$post_num,'status'=>2],['id'=>$order_id]);
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }


    }
}