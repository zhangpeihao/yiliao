<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/24
 * Time: 17:48
 */
namespace app\common\model;

use think\Model;

class MallProductMenu extends Model{
    protected $name='mall_product_menu';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';


}