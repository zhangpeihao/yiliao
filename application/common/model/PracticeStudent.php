<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/5
 * Time: 15:36
 */
namespace app\common\model;

use think\Model;

class PracticeStudent extends Model
{
    protected $name='practice_student';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';


}