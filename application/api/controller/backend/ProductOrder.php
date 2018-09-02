<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/9
 * Time: 11:37
 */
namespace app\api\controller\backend;

use app\common\controller\Api;
use app\common\model\MallPay;
use app\common\model\MallProductOrder;
use app\common\model\MallStoreAttr;
use app\common\model\MallStoreLog;

/**
 * 教务端查看配件、金币商品订单
 * Class ProductOrder
 * @package app\api\controller\backend
 */
class ProductOrder extends Api
{
    protected $noNeedLogin='';

    protected $noNeedRight='*';

    /**
     * 获取订单列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="type", type="int", required=true, description="类型：1配件、2课程、3金币")
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
     * @ApiReturnParams   (name="wuliu_info", type="array", required=true, description="发货信息：post_num快递编号,post_type快递类型，用于调用物流轨迹接口查询")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/11 15:35
     */
    public function get_list()
    {
        $type=$this->request->request('type',1,'intval');
        $page=$this->request->request('page',1,'intval');
        $page_size=$this->request->request('page_size',20,'intval');

        $map['status']=['gt',0];
        $map['type']=$type;
        $map['agency_id']=$this->auth->agency_id;
        $data=model('MallProductOrder')->where($map)
//            ->field('id,uid,out_trade_no,tmp_paysn,pid,title,price,total,money,credit,credit_money,post_type,pay_type,num,post_num,address,status,from,remark,lesson_id,lesson_count,already_lesson,ctime,utime,creator,updator,student_id')
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
     * @ApiReturnParams   (name="wuliu_info", type="array", required=true, description="发货信息：post_num快递编号,post_type快递类型，用于调用物流轨迹接口查询")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/14 18:22
     */
    public function get_detail()
    {
        $order_id=$this->request->request('order_id',0,'intval');
        if (empty($order_id)){$this->error('请指定订单');}
        $check=model('MallProductOrder')->where('id',$order_id)
//            ->where('uid',$this->auth->id)
            ->find();
        if (empty($check)){$this->error('订单不存在');}
        $this->success('查询成功',$check);
    }

    /**
     * 教务端商品下单
     * @ApiMethod   (POST)
     * @ApiParams   (name="product_id", type="int", required=true, description="商品id")
     * @ApiParams   (name="num", type="int", required=true, description="商品件数，默认为1")
     * @ApiParams   (name="attr", type="string", required=true, description="规格：直接传规格名称即可")
     * @ApiParams   (name="real_salary", type="int", required=true, description="实收金额")
     * @ApiParams   (name="discount", type="int", required=true, description="折扣，小数形式")
     * @ApiParams   (name="free_money", type="int", required=true, description="其他优惠")
     * @ApiParams   (name="fee", type="int", required=true, description="其他附加金额")
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiParams   (name="remark", type="string", required=true, description="备注")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/10 10:32
     */
    public function create_order()
    {
        $product_id=request()->post('product_id',0,'intval');
        $num=request()->post('num',1,'intval');
        $real_salary=request()->post('real_salary',0,'floatval');
        $discount=request()->post('discount',0,'floatval');
        $free_money=request()->post('free_money',0,'floatval');
        $fee=request()->post('fee',0,'floatval');
        $student_id=request()->post('student_id',0,'intval');
        $already_lesson=request()->post('already_lesson',0,'intval');
        $remark=request()->post('remark','','strval');
        $attr=request()->post('attr','','strval');

        if (empty($product_id)){$this->error('请选择商品');}
        $uid=0;
        if (empty($student_id)){$this->error('请选择学员');}else{
            $student=db('student')->find($student_id);
            if ($student){
                $uid=db('user')->where('mobile',$student['mobile'])->value('id');
                if (empty($uid)){$uid=0;}
            }
        }
        if (empty($real_salary)){$this->error('请填写实收金额');}
        $product=\app\common\model\MallProduct::get($product_id);

        if (empty($attr)){
            $check_attr_exsit=db('mall_store_attr')->where('pid',$product['store_id'])->count();
            if ($check_attr_exsit>0){
                $this->error('请选择规格');
            }
        }
        $data=[
            'uid'=>$uid,
            'out_trade_no'=>getordersn($this->auth->id,2),
            'tmp_paysn'=>getordersn($this->auth->id,9),//统一支付订单号
            'pid'=>$product_id,
            'lesson_id'=>$product['lesson_id'],
            'lesson_count'=>$product['lesson_count'],
            'already_lesson'=>$already_lesson,
            'student_id'=>$student_id,
            'title'=>$product['title'],
            'price'=>$product['price'],
            'fee'=>$fee,
            'free_money'=>$free_money,
            'total'=>$product['price']*$num+$fee,
            'money'=>$real_salary,
            'discount'=>$discount,
            'pay_type'=>5,//1=>'积分',2=>'支付宝',3=>'微信',4=>'余额支付',5=>'到店支付'
            'num'=>$num,
            'status'=>1,
            'remark'=>$remark,
            'from'=>'B端',
            'creator'=>$this->auth->id,
            'type'=>$product['type'],
            'attr'=>$attr
        ];
        $order=new MallProductOrder();
        $info=$order->save($data);
        if ($info){
            //结算库存
            MallStoreLog::add_store_log($order->id,1);

            $pay_log = array(
                'total'=>$data['total'],//订单应支付
                'money' => $data['money'],//收到支付
                'status' => 1,
                'type' => 3,//1=>'在线支付',2=>'余额支付',3=>'线下付款',4=>'兑换'
                'ctype' => 0,
                'onlinetype' => 'shop',
                'remark' => '到店支付',
                'pay_detail'=>"",
                'pay_sn'=>$data['out_trade_no'],
                'out_trade_no'=>$data['out_trade_no'],
                'uid'=>$data['uid'],
                'creator'=>$this->auth->id
            );
            $res=MallPay::create($pay_log);
            //添加记账
            if ($res){
                //1=>'投资',2=>'支出',3=>'收入'
                $res=\app\common\model\Account::addAccount($uid,$data['money'],3,time(),$data['out_trade_no'],$this->auth->id,'B端收款','购买'.$product['title'],$student_id);
            }
            $this->success('下单成功');
        }else{
            $this->error('下单失败');
        }
    }
}