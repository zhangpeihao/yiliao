<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/14
 * Time: 16:47
 */
namespace app\common\model;

use think\Model;

class UserLevel extends Model{
    protected $name='user_level';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';


}