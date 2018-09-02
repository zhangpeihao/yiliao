<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/7
 * Time: 17:25
 */
namespace app\test\controller;

use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use think\Controller;

class MonoLog extends Controller{
    public function index()
    {
        $log=new Logger('name');
        $log->pushHandler(new StreamHandler('12312.log'),Logger::WARNING);
//        $log->pushHandler(new NativeMailerHandler(
//            '2846359640@qq.com',
//            'test',
//            '1227058959@qq.com'
//        ),Logger::WARNING);
        $log->warning('Foo');
        $res=$log->error('bar');
        $log->info('My logger is now ready');
        $log->addInfo('new user',['username'=>'tetst']);
        dump($res);
    }
}