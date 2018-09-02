<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/6
 * Time: 16:49
 */
namespace app\common\model;

use think\Model;

class MallAccountLog extends Model
{
    protected $name='mall_account_log';

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
            return db('mall_account_log')->field('log_id',true)->where('log_id',$data['log_id'])->order('id desc')->select();
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