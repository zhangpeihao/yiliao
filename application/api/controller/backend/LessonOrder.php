<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/6
 * Time: 15:12
 */
namespace app\api\controller\backend;

use app\common\controller\Api;
use app\common\model\MallPay;
use app\common\model\MallProductOrder;
use app\common\model\MallProductOrderLog;
use app\common\model\Student;
use think\Db;
use think\Validate;

/**
 * 教务端管理课程订单
 * Class LessonOrder
 * @package app\api\controller\backend
 */
class LessonOrder extends Api
{
    protected $noNeedRight='*';

    protected $noNeedLogin='';

    /**
     * 获取课程订单列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="keyword", type="string", required=false, description="查询关键词：课程名称、学员名称、手机号")
     * @ApiParams   (name="lesson_name", type="string", required=false, description="课程名称")
     * @ApiParams   (name="student_name", type="string", required=false, description="学员名称")
     * @ApiParams   (name="student_id", type="int", required=false, description="学员id")
     * @ApiParams   (name="mobile", type="string", required=false, description="手机号")
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
     * @date: 2018/7/6 15:13
     */
    public function get_order_list()
    {
        $keyword=request()->request('keyword','','strval');
        $lesson_name=request()->request('lesson_name','','strval');
        $mobile=request()->request('mobile','','strval');
        $student_name=request()->request('student_name','','strval');
        $student_id=request()->request('student_id',0,'intval');
        $page=$this->request->request('page',1,'intval');
        $page_size=$this->request->request('page_size',20,'intval');

        $map=[];
        if ($keyword){
            if (is_mobile($keyword)){
                $uid=\db('user')->where('mobile',$keyword)->where('status',1)->value('id');
                if ($uid){$map['uid']=$uid;}
            }else{
                $may_lesson_product=\db('mall_product')->where('title','like','%'.$keyword.'%')->where('status',1)->column('id');
                $may_student=\db('student')->where('username','like','%'.$keyword.'%')->where('status',1)->column('id');
            }
        }
        if ($student_id){
            $map['student_id']=$student_id;
        }
//        if ($lesson_name){$map=[];}
//        if ($mobile){$map=[];}
//        if ($student_name){$map=[];}
        $map['status']=['gt',0];
        $map['lesson_id']=['gt',0];
        $map['agency_id']=$this->auth->agency_id;
        $order=model('MallProductOrder')->where($map);
        if ($keyword){
            $order->whereOr('pid','in',$may_lesson_product)
                ->whereOr('student_id','in',$may_student);
        }
//            ->field('id,uid,out_trade_no,tmp_paysn,pid,title,price,total,money,credit,credit_money,pay_type,num,post_num,address,status,from,remark,post_type,student_id,lesson_id,already_lesson,lesson_count,ctime,utime')
        $data=$order->field('from,form_id,openid,share_code,user_duihuan_code,user_coupon_id,self_accept',true)
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
        $order_id=request()->request('order_id',0,'intval');
        if (empty($order_id)){$this->error('请指定订单');}
        $check=model('MallProductOrder')->where('id',$order_id)
//            ->where('uid',$this->auth->id)
            ->find();
        if (empty($check)){$this->error('订单不存在');}
        $this->success('查询成功',$check);
    }

    /**
     * 教务端创建课程订单
     * @ApiMethod   (POST)
     * @ApiParams   (name="product_id", type="int", required=true, description="课程商品id")
     * @ApiParams   (name="num", type="int", required=true, description="商品件数，默认为1")
     * @ApiParams   (name="real_salary", type="int", required=true, description="实收金额")
     * @ApiParams   (name="discount", type="int", required=true, description="折扣，小数形式")
     * @ApiParams   (name="free_money", type="int", required=true, description="其他优惠")
     * @ApiParams   (name="fee", type="int", required=true, description="其他附加金额")
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiParams   (name="already_lesson", type="int", required=true, description="已上课节数")
     * @ApiParams   (name="remark", type="string", required=true, description="备注")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/6 15:59
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

        if (empty($product_id)){$this->error('请选择课程商品');}
        $uid=0;
        if (empty($student_id)){$this->error('请选择学员');}else{
            $student=db('student')->find($student_id);
            if (empty($student)){$this->error('该学员不存在');}
            $uid=db('user')->where('mobile',$student['mobile'])->value('id');
        }
        if (empty($real_salary)){$this->error('请填写实收金额');}

        $product=\app\common\model\MallProduct::get($product_id);
        $data=[
            'uid'=>intval($uid),
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
            'type'=>2
        ];
        $info=MallProductOrder::create($data,true);
        if ($info){
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
                'uid'=>intval($data['uid']),
                'creator'=>$this->auth->id
            );
            $res=MallPay::create($pay_log);
            //添加记账
            if ($res){
                //1=>'投资',2=>'支出',3=>'收入'
                $res=\app\common\model\Account::addAccount($uid,$data['money'],3,time(),$data['out_trade_no'],$this->auth->id,'B端收款','购买'.$product['title'],$student_id);
            }
            //更新学员剩余课节数
            /*if ($student_id){
                model('student')->where('id',$student_id)
                    ->inc('rest_lesson',$product['lesson_count'])
                    ->dec('rest_lesson',$already_lesson)
                    ->update();
            }*/
            $this->success('下单成功');
        }else{
            $this->error('下单失败');
        }
    }

    /**
     * 修改订单内容
     * @ApiMethod   (POST)
     * @ApiParams   (name="order_id", type="int", required=true, description="订单号")
     * @ApiParams   (name="product_id", type="int", required=true, description="课程商品id")
     * @ApiParams   (name="num", type="int", required=true, description="商品件数，默认为1")
     * @ApiParams   (name="real_salary", type="int", required=true, description="实收金额")
     * @ApiParams   (name="discount", type="int", required=true, description="折扣，小数形式")
     * @ApiParams   (name="free_money", type="int", required=true, description="其他优惠")
     * @ApiParams   (name="fee", type="int", required=true, description="其他附加金额")
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiParams   (name="already_lesson", type="int", required=true, description="已上课节数")
     * @ApiParams   (name="remark", type="string", required=true, description="备注")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/6 16:36
     */
    public function edit_order()
    {
        $order_id=request()->post('order_id',0,'intval');
        $product_id=request()->post('product_id',0,'intval');
        $num=request()->post('num',1,'intval');
        $real_salary=request()->post('real_salary',0,'floatval');
        $discount=request()->post('discount',0,'floatval');
        $free_money=request()->post('free_money',0,'floatval');
        $fee=request()->post('fee',0,'floatval');
        $student_id=request()->post('student_id',0,'intval');
        $already_lesson=request()->post('already_lesson',0,'intval');
        $remark=request()->post('remark','','strval');

        $order_info=\db('mall_product_order')->find($order_id);
//        if (empty($order_info)){$this->error('订单不存在');}
        $data=[
            'id'=>$order_id,
            'pid'=>$product_id,
            'num'=>$num,
            'money'=>$real_salary,
            'discount'=>$discount,
            'free_money'=>$free_money,
            'fee'=>$fee,
            'student_id'=>$student_id,
            'already_lesson'=>$already_lesson,
            'remark'=>$remark
        ];
        $table_info=Db::table('information_schema.columns')->where('table_name','fa_mall_product_order')->column('COLUMN_COMMENT','COLUMN_NAME');
        $count=0;
        $log_id=md5($this->auth->id.'-'.$order_id.'-'.time());
        foreach ($data as $k=>$v){
            if ($v!=$order_info[$k]){
                $count++;
                $info=MallProductOrderLog::create([
                    'log_id'=>$log_id,
                    'order_id'=>$order_id,
                    'field'=>$k,
                    'last_value'=>$order_info[$k],
                    'modify_value'=>$v,
                    'creator'=>$this->auth->id,
                    'remark'=>$table_info[$k]
                ]);
                //更新学员剩余课节数
                /*if ($k=='already_lesson'){
                    if ($student_id){
                        $student_model=model('student')->where('id',$student_id);
                        $cal_num=$v-$order_info[$k];
                        if ($cal_num>0){
                            $student_model->dec('rest_lesson',$cal_num)->update();;
                        }elseif ($cal_num<0){
                            $student_model->inc('rest_lesson',abs($cal_num))->update();;
                        }
                    }
                }*/
            }
        }
        if ($count>0){
            $data['updator']=$this->auth->id;
            $res=MallProductOrder::update($data,['id'=>$order_id],true);
            //添加记账
            if ($res){
                //1=>'投资',2=>'支出',3=>'收入'
                \app\common\model\Account::updateAccount($order_info['uid'],$data['money'],3,time(),$order_info['out_trade_no'],$this->auth->id,'B端收款','购买'.$order_info['title']);
            }
            $this->success('操作成功');
        }else{
            $this->success('未作任何修改');
        }

    }

    /**
     * 作废订单
     * @ApiMethod   (POST)
     * @ApiParams   (name="out_trade_no", type="string", required=true, description="订单号")
     * @ApiParams   (name="order_id", type="int", required=true, description="订单id")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/6 17:05
     */
    public function refuse_order()
    {
        $out_trade_no=request()->post('out_trade_no','','strval');
        $order_id=request()->post('order_id',0,'intval');
        if (empty($out_trade_no)){$this->error('订单号不能为空');}
        if (empty($order_id)){$this->error('订单id不能为空');}
        $check=MallProductOrder::get(['id'=>$order_id,'out_trade_no'=>$out_trade_no]);
        if (empty($check)){$this->error('订单不存在');}
        $info=MallProductOrder::update(['status'=>0,'updator'=>$this->auth->id],['id'=>$order_id]);
        if ($info){
//            $table_info=Db::table('information_schema.columns')->where('table_name','fa_mall_product_order')->column('COLUMN_COMMENT','COLUMN_NAME');
            MallProductOrderLog::create([
                'order_id'=>$order_id,
                'field'=>'status',
                'last_value'=>$check['status'],
                'modify_value'=>0,
                'creator'=>$this->auth->id,
                'remark'=>'订单作废'
            ]);
            //更新学员剩余课节数
            /*try{
                model('student')->where('id',$check['student_id'])
                    ->dec('rest_lesson',$check['product_info']['lesson_count'])
                    ->inc('rest_lesson',$check['already_lesson'])
                    ->update();
            }catch (\Exception $e){}*/
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }
}