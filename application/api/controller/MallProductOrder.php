<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/11
 * Time: 15:32
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 商城订单
 * Class MallProductOrder
 * @package app\api\controller
 */
class MallProductOrder extends Api{
    protected $noNeedRight='*';

    /**
     * 获取我的订单列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="page", type="int", required=true, description="当前分页")
     * @ApiParams   (name="page_size", type="int", required=true, description="分页大小")
     * @ApiReturnParams   (name="id", type="int", required=true, description="订单id")
     * @ApiReturnParams   (name="uid", type="int", required=true, description="下单人uid")
     * @ApiReturnParams   (name="out_trade_no", type="string", required=true, description="统一支付订单号")
     * @ApiReturnParams   (name="tmp_paysn", type="string", required=true, description="单个订单号，用于未结算订单，单个商品再次支付")
     * @ApiReturnParams   (name="pid", type="int", required=true, description="商品id")
     * @ApiReturnParams   (name="title", type="string", required=true, description="标题")
     * @ApiReturnParams   (name="price", type="float", required=true, description="价格")
     * @ApiReturnParams   (name="total", type="float", required=true, description="总金额")
     * @ApiReturnParams   (name="money", type="float", required=true, description="已支付金额")
     * @ApiReturnParams   (name="credit", type="float", required=true, description="金币数")
     * @ApiReturnParams   (name="credit_money", type="float", required=true, description="金币抵扣人民币数")
     * @ApiReturnParams   (name="pay_type", type="int", required=true, description="支付方式")
     * @ApiReturnParams   (name="num", type="int", required=true, description="数量")
     * @ApiReturnParams   (name="post_num", type="string", required=true, description="邮寄编号")
     * @ApiReturnParams   (name="address", type="string", required=true, description="收货地址")
     * @ApiReturnParams   (name="status", type="int", required=true, description="状态：-1=>'订单取消',0=>'待支付',1=>'已支付',2=>'已发货',3=>'已签收',4=>'已退款'")
     * @ApiReturnParams   (name="from", type="string", required=true, description="订单来源")
     * @ApiReturnParams   (name="remark", type="string", required=true, description="备注")
     * @ApiReturnParams   (name="product_info", type="array", required=true, description="产品信息：logo封面，price单价，id商品id,cid商品分类，category_text分类,lesson_info课程信息")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/11 15:35
     */
    public function get_list()
    {
        $page=$this->request->request('page',1,'intval');
        $page_size=$this->request->request('page_size',20,'intval');

        $map['uid']=$this->auth->id;
        $map['agency_id']=$this->auth->agency_id;

        $data=model('MallProductOrder')->where($map)
//            ->field('id,uid,out_trade_no,tmp_paysn,pid,title,price,total,money,credit,credit_money,post_type,pay_type,num,post_num,address,status,from,remark,lesson_id,lesson_count,already_lesson,ctime,utime,creator,student_id')
            ->field('self_accept,user_coupon_id,user_duihuan_code,share_code,form_id,openid,extra_attr',true)
            ->order('id desc')->paginate($page_size,[],['page'=>$page]);
        $data=$data->jsonSerialize();
        $this->success('查询成功',$data);
    }


    /**
     * 获取订单详情
     * @ApiMethod   (GET)
     * @ApiParams   (name="order_id", type="int", required=true, description="订单id")
     * @ApiReturnParams   (name="address", type="string", required=true, description="收货地址")
     * @ApiReturnParams   (name="credit", type="int", required=true, description="金币数")
     * @ApiReturnParams   (name="credit_money", type="int", required=true, description="金币抵扣现金数")
     * @ApiReturnParams   (name="credit_status", type="int", required=true, description="金币支付状态：0未支付，1已支付")
     * @ApiReturnParams   (name="ctime", type="string", required=true, description="创建时间")
     * @ApiReturnParams   (name="fee", type="float", required=true, description="运费")
     * @ApiReturnParams   (name="from", type="string", required=true, description="来源")
     * @ApiReturnParams   (name="id", type="int", required=true, description="订单id")
     * @ApiReturnParams   (name="money", type="int", required=true, description="实际支付金额")
     * @ApiReturnParams   (name="num", type="int", required=true, description="数量")
     * @ApiReturnParams   (name="out_trade_no", type="string", required=true, description="订单号")
     * @ApiReturnParams   (name="pay_type", type="string", required=true, description="支付方式，已转义：1=>'积分',2=>'支付宝',3=>'微信',4=>'余额支付',5=>'到店支付'")
     * @ApiReturnParams   (name="pid", type="int", required=true, description="商品id")
     * @ApiReturnParams   (name="post_num", type="string", required=true, description="邮寄编号")
     * @ApiReturnParams   (name="post_type_text", type="string", required=true, description="邮寄类型：自提，寄送")
     * @ApiReturnParams   (name="price", type="float", required=true, description="单价")
     * @ApiReturnParams   (name="product_info", type="array", required=true, description="商品信息，数组")
     * @ApiReturnParams   (name="remark", type="string", required=true, description="备注")
     * @ApiReturnParams   (name="title", type="string", required=true, description="订单简称")
     * @ApiReturnParams   (name="tmp_paysn", type="string", required=true, description="临时支付编号")
     * @ApiReturnParams   (name="total", type="float", required=true, description="订单总金额")
     * @ApiReturnParams   (name="uid", type="int", required=true, description="用户id")
     * @ApiReturnParams   (name="status", type="int", required=true, description="状态,转义之后参考status_text")
     * @ApiReturnParams   (name="status_text", type="string", required=true, description="状态，已转义：-1=>'订单取消',0=>'待支付',1=>'已支付',2=>'已发货',3=>'已签收',4=>'已退款'")
     * @ApiReturnParams   (name="utime", type="string", required=true, description="更新时间")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/14 18:22
     */
    public function get_detail()
    {
        $order_id=$this->request->request('order_id',0,'intval');
        if (empty($order_id)){$this->error('请指定订单');}
        $check=model('MallProductOrder')->where('id',$order_id)->where('uid',$this->auth->id)->find();
        if (empty($check)){$this->error('订单不存在');}
        $this->success('查询成功',$check);
    }
}