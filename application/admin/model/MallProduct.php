<?php

namespace app\admin\model;

use think\Model;

class MallProduct extends Model
{
    // 表名
    protected $name = 'mall_product';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    
    // 追加属性
    protected $append = [
        'begin_time_text',
        'end_time_text',
        'is_recommend_text',
        'status_text',
        'ctime_text',
        'utime_text',
        'category',
        'type_text'
    ];

    public function getLevelDiscountAttr($value,$data)
    {
        $list=[1=>'会员折扣',0=>'会员不折扣'];
        return $list[$value];
    }

    public function getCategoryAttr($value,$data)
    {
        return (string)db('mall_category')->where('id',$data['cid'])->value('name');
    }

    public function getTypeTextAttr($value,$data)
    {
        $list=[1=>'商品',2=>'课程',3=>'金币商城'];
        if ($data['type']){
            return $list[$data['type']];
        }else{
            return "";
        }
    }

    
    public function getIsRecommendList()
    {
        return [0=>'否',1=>'是'];
    }     

    public function getStatusList()
    {
        return [0=>'禁用',1=>'正常'];
    }     


    public function getBeginTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['begin_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getEndTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['end_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getIsRecommendTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_recommend'];
        $list = $this->getIsRecommendList();
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

    protected function setBeginTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setEndTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
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
