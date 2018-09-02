<?php

namespace app\admin\model;

use think\Model;

class CameraPower extends Model
{
    // 表名
    protected $name = 'camera_power';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'power_type_text',
        'status_text',
        'ctime_text',
        'utime_text',
        'username',
        'creator_name'
    ];

    public function getCreatorNameAttr($value,$data)
    {
        return (string)db('user')->where('id',$data['creator'])->value('username');
    }

    public function getUsernameAttr($value,$data)
    {
        return (string)db('user')->where('id',$data['uid'])->value('username');
    }
    
    public function getPowerTypeList()
    {
        return ['1' => '授权给个人','2'=>'授权给机构'];
    }     

    public function getStatusList()
    {
        return ['1' =>'启用',0=>'禁用'];
    }     


    public function getPowerTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['power_type'];
        $list = $this->getPowerTypeList();
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
