<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/24
 * Time: 17:26
 */
namespace app\common\model;

use think\Model;

class UserAddress extends Model{
    protected $name='user_address';

    protected $autoWriteTimestamp='int';

    protected $createTime='create_time';

    protected $updateTime='update_time';

    protected $dateFormat='Y-m-d H:i';


}