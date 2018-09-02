<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/24
 * Time: 16:00
 */
namespace app\common\model;

use think\Model;

class CameraPower extends Model
{
    protected $name='camera_power';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    protected $append=['power_type_text'];

    public function getPowerTypeTextAttr($value,$data)
    {
        $list=[1=>'授权个人',2=>'批量授权'];
        return $list[$data['power_type']];
    }

}