<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/24
 * Time: 15:45
 */
namespace app\common\model;

use think\Model;

class Camera extends Model
{
    protected $name='camera';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=['creator_info'];

    public function getCreatorInfoAttr($value,$data)
    {
        $res=\model('user')->field('id,username,avatar,mobile')->find();
        if (empty($res)){
            return (object)$res;
        }else{
            return $res->toArray();
        }
    }

}