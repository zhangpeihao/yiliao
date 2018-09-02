<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/25
 * Time: 16:14
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 商城订单支付
 * Class MallPay
 * @package app\api\controller
 */
class MallPay extends Api{

    protected $noNeedLogin=[];

    protected $noNeedRight='*';

    /**
     * 获取APP支付参数
     * @ApiMethod   (POST)
     * @ApiParams   (name="out_trade_no", type="string", required=true, description="订单号，购物下单时，请使用out_trade_no，未支付订单结算，请使用tmp_paysn")
     * @ApiParams   (name="pay_type", type="int", required=true, description="支付方式：1=>'积分',2=>'支付宝',3=>'微信',4=>'余额支付',5=>'兑换码'")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/25 16:14
     */
    public function get_pay_config()
    {
        $out_trade_no=request()->post('out_trade_no','','strval');
        $pay_type=request()->post('pay_type',0,'intval');
        if (empty($out_trade_no)){$this->error('请指定订单号');}
        if (empty($pay_type)){$this->error('请指定支付方式');}

        if ($pay_type==2){
            $res=\app\common\model\MallPay::get_wx_config($out_trade_no);
        }elseif ($pay_type==3){
            $res=\app\common\model\MallPay::ali_pay_config($out_trade_no);
        }
        if ($res){
            $this->success('获取成功',$res);
        }else{
            $this->error('获取失败');
        }
    }

}