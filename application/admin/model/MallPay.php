<?php

namespace app\admin\model;

use think\Model;

class MallPay extends Model
{
    // 表名
    protected $name = 'mall_pay';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'status_text',
        'ctype_text',
        'ctime_text',
        'utime_text',
        'online_type_text'
    ];


    public function getOnlineTypeList()
    {
        return [1=>'支付宝',2=>'微信',3=>'现金',4=>'团购券',5=>'赠送',6=>'会员'];
    }

    public function getTypeList()
    {
        return [1=>'在线支付',2=>'余额支付',3=>'线下付款',4=>'兑换'];
    }

    public function getTypeTextAttr($value,$data)
    {
        $list=$this->getTypeList();
        return $list[$data['type']];
    }

    public function getOnlineTypeTextAttr($value,$data)
    {
        if ($data['onlinetype']=='shop'){return "到店支付";}
        $list=$this->getOnlineTypeList();
        return $list[$data['onlinetype']];
    }

    public function getStatusList()
    {
        return [0=>'未支付',1=>'支付成功'];
    }     

    public function getCtypeList()
    {
        return [0=>'消费',1=>'充值'];
    }     


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getCtypeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['ctype'];
        $list = $this->getCtypeList();
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
