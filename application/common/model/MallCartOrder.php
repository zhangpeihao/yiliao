<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/8
 * Time: 17:42
 */
namespace app\common\model;

use think\Model;

class MallCartOrder extends Model
{
    protected $name='mall_cart_order';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';


}