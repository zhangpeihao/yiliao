<?php

namespace app\admin\model;

use think\Model;

class MallWuliu extends Model
{
    // 表名
    protected $name = 'mall_wuliu';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'status_text',
        'ctime_text',
        'utime_text',
        'wuliu_company'
    ];

    public function getWuliuCompanyAttr($value,$data)
    {
        return (string)db('mall_wuliu_company')->where('code',$data['post_type'])->value('name');
    }

    
    public function getStatusList()
    {
        return [0=>'待发货',1=>'已发货',2=>'运输中',3=>'已签收',4=>'已退回'];
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
