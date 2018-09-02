<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/16
 * Time: 11:36
 */
namespace app\common\model;

use think\Model;

class SheduleModifyLog extends Model
{
    protected $name='shedule_modify_log';


    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    protected $append=['creator_info','log_list'];

    protected $dateFormat='Y-m-d H:i';


    public function getLogListAttr($value,$data)
    {
        if (empty($data['log_id'])){
            return [];
        }else{
            return db('shedule_modify_log')->field('log_id',true)->where('log_id',$data['log_id'])->order('id desc')->select();
        }
    }

    public function getCreatorInfoAttr($value,$data)
    {
        $res=\model('User')->field('id,username,mobile,avatar,gender')->find($data['creator']);
        if ($res){
            return $res->toArray();
        }else{
            return (object)[];
        }
    }
}