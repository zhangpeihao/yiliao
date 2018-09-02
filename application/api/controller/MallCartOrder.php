<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/8
 * Time: 17:17
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\MallProductOrder;
use app\common\model\MallStoreLog;
use think\Db;

/**
 * 商城、购物车结算
 * Class MallCartOrder
 * @package app\api\controller
 */
class MallCartOrder extends Api{
    protected $noNeedRight='*';

    /**
     * 计算订单相关费用
     * @ApiMethod   (POST)
     * @ApiParams   (name="cart_ids", type="string", required=true, description="购物车id,多个用英文逗号分隔")
     * @ApiParams   (name="pay_type", type="int", required=true, description="支付方式 1=>'在线支付',5=>'到店支付'")
     * @ApiReturnParams   (name="product_price", type="int", required=true, description="产品总价")
     * @ApiReturnParams   (name="post_fee", type="int", required=true, description="运费")
     * @ApiReturnParams   (name="dis_rmb", type="int", required=true, description="可抵扣人民币")
     * @ApiReturnParams   (name="dis_score", type="int", required=true, description="可抵扣金币数")
     * @ApiReturnParams   (name="score", type="int", required=true, description="当前账户金币数")
     * @ApiReturnParams   (name="level_rmb", type="int", required=true, description="会员等级折扣：人民币,当等于0时，不显示")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/11 14:33
     */
    public function get_credit_cal()
    {
//        $price=$this->request->post('price',0,'intval');
        $cart_ids=$this->request->post('cart_ids','','strval');
        $pay_type=$this->request->post('pay_type',0,'intval');
        $cart_ids=explode(',',$cart_ids);
        $rate_score=config('site.rmb_score');
        if (empty($rate_score)){$rate_score=10;}
        $post_fee=0;
        $price=0;
        foreach ($cart_ids as $v) {
            $check = \app\common\model\MallCart::get(['id' => $v, 'uid' => $this->auth->id, 'status' => 1]);
            if (!empty($check)) {
                //累计运费
                if ($pay_type == 5) {
                    $fee = 0;
                } else {
                    $fee = $check['product_info']['post_fee'];
                }
                $post_fee += $fee;
                $price+=$check['product_info']['price']*$check['num'];
            }
        }
        $level_info=$this->auth->level_info;
        $level_rmb=0;
        if ($level_info['discount']){
            $price=$price*$level_info['discount'];
            $level_rmb=abs($price*(1-$level_info['discount']));
        }
        //产品价格转换成金币数
        $user_score_rmb=$this->auth->score_rmb;
        if ($price<100){
            $credit=0;
            $score=0;
        }else{
            if ($user_score_rmb>=$price){
                $credit=$price;
                $score=$price*$rate_score;
            }else{
                $credit=$user_score_rmb;
                $score=$user_score_rmb*$rate_score;
            }
        }
        $data=[
            'product_price'=>$price,
            'post_fee'=>$post_fee,
            'score'=>$this->auth->score,
            'dis_rmb'=>intval($credit),
            'dis_score'=>intval($score),
            'level_rmb'=>floatval($level_rmb)
        ];
        $this->success('查询成功',$data);
    }
    
    /**
     * 购物车结算
     * @ApiMethod   (POST)
     * @ApiParams   (name="cart_id", type="string", required=true, description="购物车id，多个英文逗号拼接")
     * @ApiParams   (name="from", type="string", required=true, description="来源：ios、android")
     * @ApiParams   (name="address_id", type="int", required=true, description="收货地址id")
     * @ApiParams   (name="pay_type", type="int", required=true, description="支付方式 1=>'在线支付',5=>'到店支付'")
     * @ApiParams   (name="credit", type="int", required=true, description="支付金币数")
     * @ApiReturnParams   (name="out_trade_no", type="string", required=true, description="用于支付的订单号")
     * @ApiReturnParams   (name="total_fee", type="float", required=true, description="需要支付金额")
     * @ApiReturnParams   (name="order_info", type="array", required=true, description="购物车结算订单信息")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/25 15:52
     */
    public function cart_order()
    {
        $cart_id=$this->request->post('cart_id',"",'strval');
        //1=>'积分',2=>'支付宝',3=>'微信',4=>'余额支付',5=>'到店支付'
        $pay_type=$this->request->post('pay_type',1,'intval');
        $credit=$this->request->post('credit',0,'intval');
        $from=$this->request->post('from','','strval');
        $address_id=$this->request->post('address_id',0,'intval');
        $cart_id=explode(',',trim($cart_id,','));

        $rule=['cart_id'=>'require','pay_type'=>'require','from'=>'require'];
        $msg=['cart_id'=>'购物车','pay_type'=>'支付方式','from'=>'来源'];
        $validate=new \think\Validate($rule,[],$msg);
        $data=['cart_id'=>$cart_id,'pay_type'=>$pay_type,'credit'=>$credit,'from'=>$from,'address_id'=>$address_id];
        if (!$validate->check($data)){$this->error($validate->getError());}
        if ($this->auth->score<$credit){$this->error('账户积分不足，无法结算');}
        //自动生成订单号 DD多订单，AD单个订单
        $out_trade_no=getordersn($this->auth->id,2);
        $address_info=[];
        if ($address_id){
            $address_info=\db('user_address')->where(['id'=>$address_id,'uid'=>$this->auth->id])->find();
        }

        //获得系统的金币转换人民币比例
        $rate_score=config('site.rmb_score');
        if (empty($rate_score)){$rate_score=10;}
        $info=0;
        $product_fee=0;
        $post_fee=0;
        $total_fee=0;
        $title=[];
        $num=0;
        Db::startTrans();
        foreach ($cart_id as $v){
            $check=\app\common\model\MallCart::get(['id'=>$v,'uid'=>$this->auth->id,'status'=>1]);
            if (!empty($check)){
                if (empty($check['product_info']['status'])){
                    $this->error($check['product_info']['title'].'已过期');
                }
                $tmp_paysn=getordersn($this->auth->id,9);//统一支付订单号
                //累计运费
                if ($pay_type==5){
                    $fee=0;
                }else{
                    $fee=$check['product_info']['post_fee'];
                }
                $post_fee+=$fee;
                $order_data=[
                    'uid'=>$this->auth->id,
                    'pid'=>$check['pid'],
                    'title'=>$check['product_info']['title'],
                    'price'=>$check['price'],
                    'fee'=>$fee,
                    'total'=>$check['price']*$check['num'],
                    'credit'=>0,
                    'credit_money'=>0,
                    'pay_type'=>$pay_type,
                    'num'=>$check['num'],
                    'out_trade_no'=>$out_trade_no,
                    'tmp_paysn'=>$tmp_paysn,
                    'status'=>0,
                    'credit_status'=>0,
                    'from'=>$from,
                    'address'=>$address_info['address'],
                    'post_type'=>$pay_type==5?0:1,
                    'self_accept'=>"",
                    'ctime'=>time(),
                    'lesson_id'=>$check['product_info']['lesson_id'],
                    'lesson_count'=>$check['product_info']['lesson_count'],
                    'attr'=>strval($check['attr_info'][0]['attr_value'])
                ];
                $product_fee+=$order_data['total'];
                $total_fee+=$product_fee+$post_fee;
                $num+=$check['num'];

                $title[]=$check['product_info']['title'];
                $order=new MallProductOrder();
                $info=$order->insertGetId($order_data);
                if ($info){
                    \app\common\model\MallCart::update(['status'=>0],['id'=>$v]);
                }
            }else{
                $this->error('请先加入购物车');
            }
        }
        if ($info){
            if ($credit>0){
                if ($this->auth->score<$credit){$this->error('账户金币不足，无法结算');}
                if ($total_fee<100){
                    $credit=0;
                }else{
                    $real_credit=floor($total_fee/(10*$rate_score))*$rate_score*10;
                    //当订单总额能够抵扣的金币数低于提交过来的金币数，则做处理
                    if ($credit>$real_credit){
                        $credit=$real_credit;
                    }
                }
            }
            $level_rmb=0;
            $level_info=$this->auth->level_info;
            if ($level_info['discount']){
                if ($level_info['discount']>1){$level_info['discount']=1;}
                $level_rmb=abs($product_fee*(1-$level_info['discount']));
            }

            $cart_order=[
                'uid'=>$this->auth->id,
                'out_trade_no'=>$out_trade_no,
                'cart_id'=>implode(',',$cart_id),
                'pay_type'=>$pay_type,
                'title'=>string_cut(implode(',',$title),100,'...'),
                'total'=>$total_fee,
                'product_fee'=>$product_fee,
                'level_rmb'=>$level_rmb,
                'post_fee'=>$post_fee,
                'credit'=>$credit,
                'credit_money'=>floatval($credit/$rate_score),
                'num'=>$num,
                'address_id'=>$address_id,
                'status'=>0
            ];
            $cart_order['total']=$cart_order['total']-$cart_order['credit_money']-$level_rmb;


            $cart_model=new \app\common\model\MallCartOrder();
            $info=$cart_model->data($cart_order)->save();
            if ($info){
                //扣除积分
                //1=>'发布练习视频',2=>'商城消费兑换',3=>'商城消费'
                $score_data=[
                    'uid'=>$this->auth->id,
                    'num'=>$credit,
                    'operate'=>-1,
                    'type'=>1,
                    'creator'=>'system',
                    'link_order'=>$out_trade_no,
                    'remark'=>'购物车结算金币抵扣'
                ];
                $res_info=\app\common\model\UserScore::changeScore($score_data);
                if ($res_info){
                    $update_data=['credit_status'=>1];
                    try{
                        //更新金币支付状态
                        Db::name('mall_product_order')->where('out_trade_no',$out_trade_no)->update($update_data);
                    }catch (\Exception $e){
                        Db::rollback();
                        $this->error('订单处理失败');
                    }
                    \app\common\model\MallCart::update(['status'=>0],['id'=>$v]);
                }else{
                    Db::rollback();
                    $this->error('账户金币结算错误，下单失败');
                }
                Db::commit();
                $this->success('下单成功',['out_trade_no'=>$out_trade_no,'total_fee'=>$cart_order['total'],'order_info'=>$cart_order]);
            }else{
                Db::rollback();
                $this->error('下单失败');
            }
        }else{
            $this->error('创建下单失败');
        }
    }


    /**
     * 金币商城订单提交，直接账户结算
     * @ApiMethod   (POST)
     * @ApiParams   (name="pid", type="int", required=true, description="产品id")
     * @ApiParams   (name="num", type="int", required=true, description="数量")
     * @ApiParams   (name="address_id", type="int", required=false, description="收货地址id")
     * @ApiParams   (name="from", type="string", required=true description="来源")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/14 18:16
     */
    public function score_order()
    {
        $pid=$this->request->post('pid',0,'intval');
        $num=$this->request->post('num',1,'intval');
        $address_id=$this->request->post('address_id',0,'intval');
        $from=$this->request->post('from','','strval');
        $uid=$this->auth->id;
        //1=>'积分',2=>'支付宝',3=>'微信',4=>'余额支付',5=>'到店支付'
        $pay_type=1;
        if (empty($pid)){$this->error('请指定商品');}
        $product_info=\app\common\model\MallProduct::get(['id'=>$pid,'status'=>1]);
        if (empty($product_info)){$this->error('商品不存在');}
        if ($this->auth->score<$product_info['credit']){$this->error('账户金币不足');}

        $address_info=[];
        if ($address_id){
            $address_info=\db('user_address')->where(['id'=>$address_id,'uid'=>$this->auth->id])->find();
        }
        $tmp_paysn=getordersn($this->auth->id,9);//统一支付订单号
        //自动生成订单号 DD多订单，AD单个订单
        $out_trade_no=getordersn($this->auth->id,2);
        $order_data=[
            'uid'=>$this->auth->id,
            'pid'=>$pid,
            'title'=>$product_info['title'],
            'price'=>0,
            'fee'=>0,
            'total'=>0,
            'credit'=>$product_info['credit'],
            'credit_money'=>0,
            'pay_type'=>$pay_type,
            'num'=>$num,
            'out_trade_no'=>$out_trade_no,
            'tmp_paysn'=>$tmp_paysn,
            'status'=>0,
            'credit_status'=>0,
            'from'=>$from,
            'address'=>$address_info['address'],
            'post_type'=>0,//0自提1寄送
            'self_accept'=>"",
            'ctime'=>time()
        ];
        //1=>'发布练习视频',2=>'商城消费兑换',3=>'商城消费'
        $score_data=[
            'uid'=>$this->auth->id,
            'num'=>$order_data['credit'],
            'operate'=>-1,
            'type'=>1,
            'creator'=>'system',
            'link_order'=>$out_trade_no,
            'remark'=>'积分商城结算金币支付'
        ];
        $res_info=\app\common\model\UserScore::changeScore($score_data);
        if ($res_info){
            $order_data['status']=1;
            $order=new MallProductOrder();
            $info=$order->insertGetId($order_data);
            if ($info){
                //结算库存
                MallStoreLog::add_store_log($info,1);

                $this->success('购买成功',$order_data);
            }
            $this->error('购买失败');
        }else{
            $this->error('支付失败');
        }
    }

}