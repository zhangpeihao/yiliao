<?php

namespace app\common\model;

use think\Model;

class UserRule extends Model
{

    // 表名
    protected $name = 'user_rule';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
    ];

    /**
     * 获取用户角色权限
     */
    public static function get_user_rule($user,$group_id=2)
    {
        $group_id=$user?$user['group_id']:$group_id;
        $group=model('UserGroup')->where('id',$group_id)->value('rules');
        $user_role=UserRule::where('id','in',$group)
                    ->where('pid',2)
                    ->field('id,name,title')->order('weigh asc')->select();
        return $user_role;
    }
}
