<?php

namespace app\admin\model;

use think\Model;

class UserAddress extends Model
{
    // 表名
    protected $name = 'user_address';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    // 追加属性
    protected $append = [
        'isdefault_text',
        'isdelete_text',
        'create_time_text',
        'update_time_text'
    ];

    
    public function getIsdefaultList()
    {
        return [1=>'默认地址',0=>'非默认'];
    }     

    public function getIsdeleteList()
    {
        return [1=>'是',0=>'否'];
    }     


    public function getIsdefaultTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['isdefault'];
        $list = $this->getIsdefaultList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsdeleteTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['isdelete'];
        $list = $this->getIsdeleteList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getCreateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['create_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getUpdateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['update_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setCreateTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setUpdateTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
