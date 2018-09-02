<?php

namespace app\admin\model;

use think\Model;

class Teacher extends Model
{
    // 表名
    protected $name = 'teacher';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'is_bind_text',
        'status_text',
        'power_text'
    ];

    public static function init()
    {
        Teacher::event('after_insert',function ($teacher){
            if ($check=User::get($teacher->id)){
                Teacher::update(['is_bind'=>1,'bind_uid'=>$check['id']],['id'=>$teacher->id]);
            }
        });
    }

    
    public function getIsBindList()
    {
        return ['1' => '已注册绑定',0=>'未绑定'];
    }     

    public function getStatusList()
    {
        return ['1' => '正常',0=>'禁用'];
    }     


    public function getIsBindTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_bind'];
        $list = $this->getIsBindList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPowerTextAttr($value,$data)
    {
        $value=$value?$value:$data['power'];

        $res=model('user_rule')->where('id','in',$value)->column('title');

        return implode(',',$res);
    }


    public static function getTreeList($selected = [])
    {
        $group_id=4;
        $list=model('user_group')->where('id',$group_id)->value('rules');
        $ruleList = collection(model('user_rule')
            ->where('status', 'normal')
            ->where('id','in',$list)
            ->whereOr('pid',0)
            ->select())->toArray();
        $nodeList = [];
        foreach ($ruleList as $k => $v)
        {
            $state = array('selected' => $v['ismenu'] ? false : in_array($v['id'], $selected),'opened'=>true);
            $nodeList[] = array('id' => $v['id'], 'parent' => $v['pid'] ? $v['pid'] : '#', 'text' => __($v['title']), 'type' => 'menu', 'state' => $state);
        }
        return $nodeList;
    }

    public static function get_power_list()
    {
        $group_id=4;
        $list=model('user_group')->where('id',$group_id)->value('rules');
        $ruleList = model('user_rule')
            ->where('status', 'normal')
            ->where('id','in',$list)
            ->column('title','id');
        return $ruleList;
    }

    public function setPowerAttr($value,$data)
    {
        $value=$value?$value:$data['power'];
        $value=explode(',',$value);
        $key=array_search(2,$value);
        unset($value[$key]);
        return implode(',',$value);
    }

}
