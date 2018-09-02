<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/7
 * Time: 17:18
 */
namespace app\test\model;

use think\Model;

class Place extends Model{
    protected $connection='mongo';

    protected $name='places';
}