<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/25
 * Time: 16:33
 */
namespace app\test\controller;

use app\common\controller\Api;

class Mq extends Api
{
    protected $noNeedRight='*';

    protected $noNeedLogin='*';

    public function test()
    {
        $connection = new \AMQPConnection(array('host' => '192.168.32.128', 'port' => '15672', 'vhost' => '/', 'login' => 'admin', 'password' => '123456'));
        $connection->connect() or die("Cannot connect to the broker!\n");
    }
    

    public function consume()
    {
        
    }

    public function publish()
    {
        
    }
}