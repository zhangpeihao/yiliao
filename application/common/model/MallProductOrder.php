<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/25
 * Time: 14:50
 */
namespace app\common\model;

use app\common\library\Auth;
use think\Model;

class MallProductOrder extends Model{
    protected $name='mall_product_order';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=['product_info','post_type_text','status_text','student_info','creator_info','wuliu_info'];

    protected static function init()
    {
        MallProductOrder::event('before_insert',function ($order){
            $auth=Auth::instance();
            $order->agency_id=$auth->agency_id;
        });
    }

    public function getWuliuInfoAttr($value,$data)
    {
        if (!empty($data['post_num'])){
            $res=\model('MallWuliu')->where('post_num',$data['post_num'])->find();
            return $res->toArray();
        }else{
            return (object)[];
        }
    }

    public function getCreatorInfoAttr($value,$data)
    {
        if (empty($data['creator'])){return (object)[];}
        $res=\model('user')->where('id',$data['creator'])->field('id,username,mobile,avatar,gender')->find();
        if ($res){
            return $res->toArray();
        }else{
            return (object)[];
        }
    }

    public function getStudentInfoAttr($value,$data)
    {
        $student_id=empty($data['student_id'])?0:$data['student_id'];
        if ($student_id){
            $res=model('Student')->where('id',$student_id)->field('id,username,avatar,mobile,gender,agency_id')->find();
            if ($res){
                return $res->toArray();
            }else{
                return (object)[];
            }
        }else{
            $res=model('User')->where('id',$data['uid'])->field('id,username,avatar,mobile,gender')->find();
            if ($res){
                return $res->toArray();
            }
            return (object)[];
        }
    }

    public function getProductInfoAttr($value,$data)
    {
        $res=\model('MallProduct')->field('id,cid,title,logo,address,price,credit,kefu,lesson_id,lesson_count,store_id')->where('id',$data['pid'])->find();
        if ($res){
            return $res->toArray();
        }else{
            return (object)[];
        }
    }

    public function getPostTypeList()
    {
        return [0=>'自提',1=>'寄送'];
    }

    public function getStatusList()
    {
        return [-1=>'订单取消',0=>'待支付',1=>'待发货',2=>'已发货',3=>'已签收',4=>'已退款'];
    }

    public function getPayTypeList()
    {
        return [1=>'积分',2=>'支付宝',3=>'微信',4=>'余额支付',5=>'到店支付'];
    }

    public function getPayTypeAttr($value,$data)
    {
        $value = $value ? $value : $data['pay_type'];
        $list=$this->getPayTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getPostTypeTextAttr($value, $data)
    {
        $value = empty($value) ? $data['post_type']:$value ;
        $list = $this->getPostTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getCtimeAttr($value, $data)
    {
        $value = $value ? $value : $data['ctime'];
        if (empty($value)){return "";}
        return is_numeric($value) ? date("Y-m-d H:i", $value) : $value;
    }


    public function getUtimeAttr($value, $data)
    {
        $value = $value ? $value : $data['utime'];
        if (empty($value)){return "";}
        return is_numeric($value) ? date("Y-m-d H:i", $value) : $value;
    }

}