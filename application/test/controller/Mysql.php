<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/8
 * Time: 15:23
 */
namespace app\test\controller;

use think\Controller;
use think\Db;
use think\Debug;

class Mysql extends Controller
{
    public function index()
    {
        $res=Db::name('user')->find();
        dump($res);
        $res=Db::name('user')->where('id','gt',0)->update(['email'=>'admin123','updatetime'=>time()]);
        dump(\db()->getLastSql());
        dump($res);
    }
}