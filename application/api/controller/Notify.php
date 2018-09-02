<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/25
 * Time: 16:51
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Account;
use EasyWeChat\Foundation\Application;
use think\Db;

/**
 * 第三方支付回调
 * Class Notify
 * @package app\api\controller
 */
class Notify extends Api{
    protected $noNeedRight='*';

    protected $noNeedLogin='*';


    public function wx_notify()
    {
        $options = [
            // 前面的appid什么的也得保留哦
            'app_id'=>'wxcfddfef2dbc38ada',
            'secret'=>'6d83f6b1790fc4735877423109cbcc0e',
            'payment' => [
                'merchant_id'        => '1489957462',
                'key'                => 'lkR54288544POdstyUtY87HhbfxzVGVB',
                'cert_path'          => '/application/mall/key/wx/apiclient_cert.pem', // XXX: 绝对路径！！！！
                'key_path'           => '/application/mall/key/wx/apiclient_key.pem',      // XXX: 绝对路径！！！！
                'notify_url'         => config('website_domain').'/api/notify/wx_notify',       // 你也可以在下单时单独设置来想覆盖它
            ],
        ];
        $app=new Application($options);
        $response = $app->payment->handleNotify(function($notify, $successful){
            $out_trade_no = $notify->out_trade_no;
            $total_fee=($notify->total_fee)/100;//单位：元
            $map=[];
            $tmp_order=explode('_',$out_trade_no);
            if ($tmp_order[0]=='DD'){
                $map['out_trade_no']=$out_trade_no;
                $order=Db::name('mall_cart_order')->where($map)->find();
            }else{
                $map['tmp_paysn']=$out_trade_no;
                $order=Db::name('mall_product_order')->where($map)->find();
            }
            if (!$order) { // 如果订单不存在
                echo 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
                exit();
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($order['status']==1) { // 假设订单字段“支付时间”不为空代表已经支付
                echo "success"; // 已经支付成功了就不再更新了
                exit();
            }
            // 用户是否支付成功
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                // 更新支付时间为当前时间
                $info=0;
                Db::startTrans();
                    $pay_log=[
                        'pay_sn'=>$notify->transaction_id,
                        'out_trade_no'=>$out_trade_no,
                        'uid'=>$order['uid'],
                        'total'=>$order['total'],//订单应支付
                        'money'=>$total_fee,//收到支付
                        'status'=>1,//数据状态0未支付1支付成功
                        'type'=>1,//付款类型1在线支付2余额支付3线下付款
                        'ctype'=>0,//交易类型（0.消费1.充值）
                        'onlinetype'=>2,//在线支付方式（1:支付宝,2:微信,3:现金,4:团购券,5:赠送,6:会员）
                        'pay_detail'=>json_encode($notify),
                        'ctime'=>time()
                    ];
                    if (Db::name('mall_pay')->where('pay_sn',$out_trade_no)->find()){
                        $info=Db::name('mall_pay')->where('pay_sn',$out_trade_no)->update($pay_log);
                    }else{
                        $info=Db::name('mall_pay')->insertGetId($pay_log);
                        //判断是否为统一订单结算：
                        if ($tmp_order[0]=='DD'){
                            if (($order['total']-$order['credit_money']-$order['level_rmb'])==$total_fee){
                                $res=Db::name('mall_cart_order')->where('out_trade_no',$out_trade_no)->update(['status'=>1,'utime'=>time()]);
                                if ($res){
                                    $info=Db::name('mall_product_order')->where('out_trade_no',$out_trade_no)->update(['status'=>1,'utime'=>time()]);
                                }else{
                                    Db::rollback();
                                    echo "fail";exit();
                                }
                                if ($info){
                                    //处理课程订单
                                    $order_list=\db('mall_product_order')->where('out_trade_no',$out_trade_no)->select();
                                    foreach ($order_list as $v){
                                        $this->deal_lesson_order($v);
                                    }
                                }
                            }
                        }else{
                            if ($order['total']==$total_fee){
                                try{
                                    $info=Db::name('mall_product_order')->where($map)->update(['status'=>1,'utime'=>time(),'money'=>$total_fee,'pay_type'=>3]);
                                }catch (\Exception $e){
                                    Db::rollback();
                                    echo "fail";exit();
                                }
                                if ($info){
                                    //处理课程订单
                                    $this->deal_lesson_order($order);
                                }
                            }
                        }

                        //入账
                        $account=new Account();
                        $account->addAccount($order['uid'],$total_fee,3,date('Y-m-d',time()),$pay_log['pay_sn'],0,'物业费微信入账');
                    }

                    Db::commit();
                    echo "success";exit();

            }else{
                echo "fail";
                exit();
            }

        });
    }

    public function alinotify()
    {
        vendor('Alipay.aop.AopClient');
        $data=input('post.');
        $message = json_encode($data);//
        $aop = new \AopClient;
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmXLnosEguDtExnUMvW7wr0j7QOSnb+K1llprwscZbLdmCMd2foBFyPRWimfE3VtP3WWP7/5ARVW0ZwheHBoOaS3ZEPfOdkrUvl5vaEIJf0+Xg1MYRyA+6kcfV1U/ClYGep9FDyvWIPNVK27zTqd+wOsIC//XiWZQUWOLy3aUIEMWQ57VQjfhAFtc84InysP2notQ7NvDO26dMTBZZ3Eb053dmCel0s8pvtsTbgudJ3eAvhcobJtCSOGKORmNfPn454Uo8fzLLLe2+iqAbX9zEZIZXMovBSHpJOwo5bza1Z4odn2xctf+ullY7CZeyvkqt+ma7nroGevsQnLHH7l3pwIDAQAB';
        $flag = $aop->rsaCheckV1($data, './application/mall/key/fx_ali_public_key.pem', "RSA2");
        if ($flag) {
            $out_trade_no=$data['out_trade_no'];
            $trade_status=$data['trade_status'];
            $data['total_fee']=$data['total_amount'];

            if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                $tmp_order=explode('_',$out_trade_no);
                $map=[];
                if ($tmp_order[0]=='DD'){
                    $map['out_trade_no']=$out_trade_no;
                    $order=Db::name('mall_cart_order')->where($map)->find();
                }else{
                    $map['tmp_paysn']=$out_trade_no;
                    $order=Db::name('mall_product_order')->where($map)->find();
                }
                if ($order['status']==1){
                    echo "success"; // 已经支付成功了就不再更新了
                    exit();
                }
                $pay_log = array(
                    'total'=>$order['total'],//订单应支付
                    'money' => $data['total_fee'],//收到支付
                    'status' => 1,
                    'type' => 1,
                    'ctype' => 0,
                    'onlinetype' => 'alipay',
                    'remark' => '支付宝支付',
                    'pay_detail'=>json_encode($data),
                    'pay_sn'=>$data['trade_no'],
                    'out_trade_no'=>$order['out_trade_no'],
                    'uid'=>$order['uid'],
                    'ctime'=>time(),
                    'utime'=>time()
                );
                Db::startTrans();
                if (Db::name('mall_pay')->where('pay_sn',$data['trade_no'])->find()){
                    $info=Db::name('mall_pay')->where('pay_sn',$data['trade_no'])->update($pay_log);
                }else{
                    $info=Db::name('mall_pay')->insertGetId($pay_log);
                }
                if ($info){
                    //入账
                    $account=new Account();
                    $account->addAccount($order['uid'],$data['total_fee'],3,date('Y-m-d',time()),$pay_log['pay_sn'],0,'订单支付宝结算入账');
                    //判断是否为统一订单结算：
                    if ($tmp_order[0]=='DD'){
                        if (($order['total']-$order['credit_money']-$order['level_rmb'])==$data['total_fee']){
                            $res=Db::name('mall_cart_order')->where('out_trade_no',$out_trade_no)->update(['status'=>1,'utime'=>time()]);
                            if ($res){
                                $info=Db::name('mall_product_order')->where('out_trade_no',$out_trade_no)->update(['status'=>1,'utime'=>time()]);
                            }else{
                                Db::rollback();
                                echo "fail";exit();
                            }
                            if ($info){
                                //处理课程订单
                                $order_list=\db('mall_product_order')->where('out_trade_no',$out_trade_no)->select();
                                foreach ($order_list as $v){
                                    $this->deal_lesson_order($v);
                                }
                            }
                        }
                    }else{
                        if ($order['fee'] == $data['total_fee']) {
                            $info=Db::name('mall_product_order')->where($map)->update([
                                'status' => 1,'pay_type'=>2, 'utime' => time()
                            ]);
                            if ($info){
                                //处理课程订单
                                $this->deal_lesson_order($order);
                            }
                            echo 'success';
                        }else{
                            echo 'fail';
                        }
                    }
                    Db::commit();
                }else{
                    Db::rollback();
                    echo "fail";
                }
            }else{
                echo 'fail';
            }
        }else{
            echo 'fail';
        }
    }


    //物流通知接口
    public function wuliu_notify()
    {

    }


    private function deal_lesson_order($order){
        return true;
        //检测是否为课程订单，如果是，则创建一份合同
        $product=\db('product')->field('type,lesson_id,lesson_count')->where('id',$order['pid'])->find();
        if ($product['type']==2){
            $contract=[];
            $uid=$order['uid'];
            $user=\db('user')->field('id,agency_id,mobile')->where('id',$uid)->find();
            $student_id=\db('student')->where('mobile',$user['mobile'])->value('id');
            $contract['agency_id']=$user['agency_id'];
            $contract['type']=1;
            $contract['sno']=\app\common\model\Contract::makeSno();
            $contract['student_id']=$student_id;
            $contract['startdate']=date('Y-m-d',time());
            $days=$product['lesson_count'];
            $contract['enddate']=date('Y-m-d',strtotime($contract['startdate']."+ $days"));
            $contract['lesson_id']=$product['lesson_id'];
            $contract['lesson_count']=$product['lesson_count'];
            $contract['price']=$product['price'];
            $contract['other_price']=0;
            $contract['total_fee']=$order['total'];
            $contract['status']=1;
            $contract['creator']=$order['uid'];
            $info=\app\common\model\Contract::create($contract,true);
            return $info;
        }
        return false;
    }
}