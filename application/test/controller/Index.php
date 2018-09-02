<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/6
 * Time: 16:21
 */
namespace app\test\controller;

use app\job\QueueClient;
use Psr\Log\Test\DummyTest;
use think\Controller;
use think\Db;
use think\Debug;
use think\Queue;

class Index extends Controller{
    protected $geo=[[117.163999,39.082846],
        [117.160459,39.084751],
        [117.160441,39.085157],
        [117.157477,39.084373],
        [117.158806,39.083896],
        [117.159543,39.081992],
        [117.160585,39.081313],
        [117.161609,39.080704],
        [117.162867,39.08069],
        [117.163442,39.08048],
        [117.164933,39.08377]];

    protected $position=[
        [2388,1306],
        [1323,508],
        [1235,315],
        [300,621],
        [793,791],
        [965,1647],
        [1292,1778],
        [1618,2124],
        [2145,2104],
        [2344,2190],
        [2712,881]
    ];
    public function index()
    {
        Debug::remark('start');
        $res=Db::name('user')->field('_id',true)->select();
        Debug::remark('end');
        dump(Debug::getRangeTime('start','end'));
        dump(Debug::getRangeMem('start','end'));

        
        Debug::remark('start2');
        $res2=Db::connect('mongo')->field('_id',true)->select();
        Debug::remark('end2');
        dump(Debug::getRangeTime('start2','end2'));
        dump(Debug::getRangeMem('start2','end2'));
        /*foreach ($res as $v){
            $res=Db::connect('mongo')->name('user')->insertGetId($v);
            dump(Db::connect('mongo')->getLastSql());
        }*/
    }

    public function test()
    {
        foreach ($this->geo as $v){
            $res=Db::connect('mongo')->name('geo')->insertGetId([
                'lng'=>$v[0],'lat'=>$v[1]
            ]);
            dump($res);
            dump(Db::connect('mongo')->getLastSql());
        }
    }

    public function gis_check()
    {
        $res=Db::connect('mongo')->name('geo')->field('lat,lng')->select();
        dump($res);
    }

    public function save_user()
    {
        $user=\db('user')->select();
        foreach ($user as $v){
            Db::connect('mongo')->name('user')->insertGetId($v);
            dump(Db::connect('mongo')->getLastSql());
        }
    }

    public function update_user()
    {
        $res=Db::connect('mongo')->name('user')->where('id','>',0)
            ->update(['agency_id'=>1,'updatetime'=>time()]);
        dump(Db::connect('mongo')->getLastSql());
        dump($res);
    }

    public function find_user()
    {
//        $map['id']=['eq',19];
        $map['mobile']=['like','151'];
        $map['email']=['like','122'];
        dump(Db::connect('mongo')->name('user')
//            ->where('id','=',19)
//            ->whereOr('mobile','like','151')
                ->where($map)
            ->select());
        dump(Db::connect('mongo')->getLastSql());
        dump(Db::connect('mongo')->name('user')->count());
    }

    public function job_list()
    {
        //加入任务队列中
//        $email_data=json_encode([
//            'address'=>'2846359640@qq.com',
//            'content'=>'测试邮件'
//        ]);
//        \think\Queue::push('app\job\QueueClient@sendMAIL', $email_data, $queue = null);
        // 1.当前任务将由哪个类来负责处理。
        //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法

        $jobHandlerClassName  = 'app\job\Hello';
        // 2.当前任务归属的队列名称，如果为新队列，会自动创建
        $jobQueueName  	  = "helloJobQueue";
        // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
        //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
        $jobData       	  = [ 'ts' => time(), 'bizId' => uniqid() , 'a' => 1 ] ;
        // 4.将该任务推送到消息队列，等待对应的消费者去执行
        $isPushed = Queue::push( $jobHandlerClassName , $jobData , $jobQueueName );
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if( $isPushed !== false ){
            echo date('Y-m-d H:i:s') . " a new Hello Job is Pushed to the MQ"."<br>";
        }else{
            echo 'Oops, something went wrong.';
        }
    }
}