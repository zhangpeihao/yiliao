<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/25
 * Time: 16:13
 */
namespace app\common\model;

use EasyWeChat\Factory;
use think\Model;

class MallPay extends Model{
    protected $name='mall_pay';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';


    public static function get_wx_config($ordersn)
    {
        $options = [
            // 前面的appid什么的也得保留哦
            'app_id'=>'wxcfddfef2dbc38ada',
            'secret'=>'6d83f6b1790fc4735877423109cbcc0e',
            'payment' => [
                'merchant_id'        => '1489957462',
                'key'                => 'lkR54288544POdstyUtY87HhbfxzVGVB',
                'cert_path'          => '/application/key/wx/apiclient_cert.pem', // XXX: 绝对路径！！！！
                'key_path'           => '/application/key/wx/apiclient_key.pem',      // XXX: 绝对路径！！！！
                'notify_url'         => config('website_domain').'/api/notify/wx_notify',       // 你也可以在下单时单独设置来想覆盖它
            ],
        ];
//        $app=new Application($options);
        $app=Factory::payment($options);
        $order=[];
        $tmp_order=explode('_',$ordersn);
        if ($tmp_order[0]=='DD'){
            $order=db('mall_product_order')->where('out_trade_no',$ordersn)->select();
            $detail=implode(',',array_column($order,'title')).'等商品';
            $total_fee=floatval(array_sum(array_column($order,'total')));
        }elseif ($tmp_order['0']=='AD'){
            $order=db('mall_product_order')->where('out_trade_no',$ordersn)->find();
            $detail=$order['title'];
            $total_fee=$order['total'];
        }
        if (empty($order)){return false;}
        $attributes = [
            'trade_type'       => 'APP', // JSAPI，NATIVE，APP...
            'body'             => "订单结算",
            'detail'           => $detail,
            'out_trade_no'     => $ordersn,
            'total_fee'        => $total_fee*100, // 单位：分
            'notify_url'       => config('website_domain').'/api/notify/wx_notify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
//            'openid'           => $openid, // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
        ];
        $result=$app->order->unify($attributes);
        //统一下单
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
        }else{
            return false;
        }

//        $json = $payment->configForPayment($prepayId); // 返回 json 字符串，如果想返回数组，传第二个参数 false
//        $json=$result->configForAppPayment($prepayId);
        return $result;
    }

    public static function ali_pay_config($ordersn)
    {
        $tmp_order=explode('_',$ordersn);
        if ($tmp_order[0]=='DD'){
            $order=db('mall_product_order')->where('out_trade_no',$ordersn)->select();
            if (empty($order)){return false;}
            $detail=implode(',',array_column($order,'title')).'等商品';
            $total_amount=array_sum(array_column($order,'total'));
        }elseif ($tmp_order['0']=='AD'){
            $order=db('mall_product_order')->where('out_trade_no',$ordersn)->find();
            if (empty($order)){return false;}
            $detail=$order['title'];
            $total_amount=$order['total'];
        }
        if (empty($order)){
            return false;
        }
        vendor('Alipay.aop.AopClient');
        vendor('Alipay.aop.request.AlipayTradeAppPayRequest');
        $content = array();
        $content['body'] = "订单结算";
        $content['subject'] = $detail;//商品的标题/交易标题/订单标题/订单关键字等
        $content['out_trade_no'] = $ordersn;//商户网站唯一订单号
        $content['timeout_express'] = '1d';//该笔订单允许的最晚付款时间
        $content['total_amount'] = floatval($total_amount);//订单总金额(必须定义成浮点型)
        $content['seller_id'] ='2088821156889929';//收款人账号
        $content['product_code'] = 'QUICK_MSECURITY_PAY';//销售产品码，商家和支付宝签约的产品码，为固定值QUICK_MSECURITY_PAY
        $content = self::argSort($content);//（固定值）
        $content = self::createLinkstring($content);
        $aop = new \AopClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";//（固定值）
        $aop->appId = '2017092208862984';
        $aop->rsaPrivateKey ='MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCMYwNLyx8+jc0Id6uuSQfI1FNhfOpPaaVnTaMeX9XXi6ahTQfnqk9lMI5OQYdkzB4chjLFmuZBo2ApOdTOHPgVVqxGyNU+Qk2G4vPeiYHjzi6+2alI4E/6tHQGDG+2mgT40BYimMQchzCD8o6Dz2s924v1UmRkqIb1MoWhqsZ9iU+XwxRcMbl+2szT5u/dtpQaD2K1Xxxznlf+gcWhP8UZCUmVZCkEbPqzKccvcrtsRC8Rg+CAb4GnIKx48pf+OSjmGMeK4f1+XoYfFyQ5UoYRK3gK/j1bihQkZfu5yte9qNeq9WMQMJZAYdgfp11VpS5nEEdMsODNoelQaOaN/ij9AgMBAAECggEAPji4QkSh8YC56lHYFuQpfhqVZjUOSOpDNDkV3iWNyv4LeZyBr20tyWSu/gJPNx69Ddlw8WJJQbheq4cFSeFPF24V5z2mPfT3FZzLh8ucdVJyJ4ajYDiDWlPWxMOIU/+Jypm35dedvCMzHphIECXDm2QOcUn2UyLaxhyBW/ksBoFIX94tWxANGtI3HCLVwpRiW+U/Qx5iHEs3Zvp3SebO/MS4WsHQiyxlyxW3ALg/vFWbMB4gPNIrjOrp2vMUFGtr1LpRL4VLR7arzDhxA+IXFjiLZ2Jwt1mnK3afK/gb0f6TbxGe3TR9uIl0rZZukKFc9gAg9cWmR/TTfuEkBVkuQQKBgQDBXm+4HrG7TXjzGzvfODag76DbUHbpY58L/4b6TLdDgLLwUNHYdEaJwln4j21KNrsSZlN2ADmAJAm/+WEtJGV6OvrzK55EZD2RYSWx8Gv1o/aKxD/PoelZibsl321DPPlEXgH4+L1v9KBhS90TlP/nwGZAacpl2ZzRVUtfRrVZ0QKBgQC523WHC1lEDFrPKzKUS8LwuNTt48BO9Z28fL8kuqbwheTFRUrxIfwBYu5D35kj7c9XMQQwD26uVNMN+PIHTx5j9O03Vo/EF5mI/dHiSAb2Tj1z36x1GEnMef1LvAlYKnh6yz63Qy2jxUojk5NTtgR0VgNJ0wEqn5io3YqF/wf7bQKBgHUFXpzRToPofZK534DV9xFsEy/GQUA6vqy3Jgth0+JxB1kxv9y7eVizGlm3Cs/H0WxwKoAV4LZwmMnp9GoqRZM0EFyLAAupkizh2rsVoXAVmwUdgPR5qss5890Wmnv/cWZzccQnXBVduJVJIPBR0pCAuiCvJQKAMEvqz2NIWkWRAoGAVTmRNw+5Kz8PFRiV5PKovYHEAiIBuTNf1WLOs6TzkC+Vq/AOYWxYBrq6z1zk+FjATxcm+HLbKg2ziiCxuzBIm0Vg0ZNb8Wtw+CSL7dthdeiCvXO/vSIaFS2LPQNItakj/grdA2RGtWZujMnLMQOyHzah42RikI0Gj8inELLVkjECgYB/HE2ZaX3cl1RbQ6EC0g5NsdtR/+ZdQlPl5hOu+bgm72LVHYZuzT2poxvBsqyZ0XL82p7OErlY6Ae0M5jd2307ibi2HnBozVJTuOw33t2p7unl70mdgmeTFUbMppoiAEKQdqNcyEr1yPzGHrunZOoVWQpKocLVNDM0Du1+8XEwcg==';
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmXLnosEguDtExnUMvW7wr0j7QOSnb+K1llprwscZbLdmCMd2foBFyPRWimfE3VtP3WWP7/5ARVW0ZwheHBoOaS3ZEPfOdkrUvl5vaEIJf0+Xg1MYRyA+6kcfV1U/ClYGep9FDyvWIPNVK27zTqd+wOsIC//XiWZQUWOLy3aUIEMWQ57VQjfhAFtc84InysP2notQ7NvDO26dMTBZZ3Eb053dmCel0s8pvtsTbgudJ3eAvhcobJtCSOGKORmNfPn454Uo8fzLLLe2+iqAbX9zEZIZXMovBSHpJOwo5bza1Z4odn2xctf+ullY7CZeyvkqt+ma7nroGevsQnLHH7l3pwIDAQAB';
//        $request = new \AlipayTradeAppPayRequest();
        $request = new \AlipayTradeAppPayRequest();
        $arr['body'] = "订单结算";
        $arr['subject'] = $detail;
        $arr['out_trade_no'] = $ordersn;
        $arr['timeout_express'] = '30m';
        $arr['total_amount'] = floatval($total_amount);
        $arr['product_code'] = 'QUICK_MSECURITY_PAY';
        $json = json_encode($arr);
        $request->setNotifyUrl(config('website_domain').'/api/notify/alinotify');
        $request->setBizContent($json);
        $response = $aop->sdkExecute($request);
//        return $response;
       return ['response'=>$response];
    }


    /*************************需要使用到的方法*******************************/
    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    private static function createLinkstring($para)
    {
        $arg = "";
        while (list ($key, $val) = each($para)) {
            $arg .= $key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }

    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    private static function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }
}