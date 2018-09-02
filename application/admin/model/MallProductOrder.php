<?php

namespace app\admin\model;

use think\Model;

class MallProductOrder extends Model
{
    // 表名
    protected $name = 'mall_product_order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'post_type_text',
        'status_text',
        'ctime_text',
        'utime_text',
        'username',
        'product_title',
        'wuliu_info'
    ];


    public function getWuliuInfoAttr($value,$data)
    {
        if (!empty($data['post_num'])){
            $res=\model('MallWuliu')->where('post_num',$data['post_num'])->find();
            return $res->toArray();
        }else{
            return (object)[];
        }
    }

    public function getProductTitleAttr($value,$data)
    {
        return (string)db('mall_product')->where('id',$data['pid'])->value('title');
    }

    public function getUsernameAttr($value,$data)
    {
        return (string)db('user')->where('id',$data['uid'])->value('username');
    }

    
    public function getPostTypeList()
    {
        return [0=>'自提',1=>'寄送'];
    }     

    public function getStatusList()
    {
            return [-1=>'订单取消',0=>'待支付',1=>'已支付',2=>'已发货',3=>'已签收',4=>'已退款'];
    }

    public function getPayTypeList()
    {
        return [1=>'积分',2=>'支付宝',3=>'微信',4=>'余额支付',5=>'兑换码'];
    }

    public function getPayTypeAttr($value,$data)
    {
        $value = $value ? $value : $data['pay_type'];
        $list=$this->getPayTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getPostTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['post_type'];
        $list = $this->getPostTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getCtimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['ctime'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getUtimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['utime'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setCtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setUtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
