<?php

namespace app\admin\model;

use think\Model;

class Practice extends Model
{
    // 表名
    protected $name = 'practice';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'type_text',
        'status_text',
        'teacher_name'
    ];

    public function getTeacherNameAttr($val,$data)
    {
        return db('teacher')->where('id',$data['teacher_id'])->value('username');
    }

    public function getTypeList()
    {
        return ['1' => '练习'];
    }     

    public function getStatusList()
    {
        return [0=>'已结束',1=>'未开始',2=>'正在进行'];
    }     


    public function getTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['type'];
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $list=[0=>'已结束',1=>'未开始',2=>'正在进行'];
        $status=$data['status'];
        if (isset($status)){
            return $list[$status];
        }else{
            return $list;
        }
    }




}
