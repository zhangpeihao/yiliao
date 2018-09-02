<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/12
 * Time: 16:10
 */
namespace app\common\model;

use think\Model;

class DisSlide extends Model
{
    protected $name='dis_slide';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $dateFormat='Y-m-d H:i';

    public function getImageAttr($value,$data)
    {
        if (!strstr($value,'http')){
            return request()->domain().$value;
        }else{
            return $value;
        }
    }

}