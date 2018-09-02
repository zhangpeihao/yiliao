<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/10
 * Time: 15:42
 */
namespace app\task\controller;

use app\common\model\MallStoreLog;
use app\common\model\VideoLog;
use Swoole\Process;
use think\Db;
use think\swoole\Server;
use util\OSS;

class Ser extends Server
{
    protected $socket="127.0.0.1";

    protected $port=9510;

    protected $sockType=SWOOLE_SOCK_TCP;

    protected $mode=SWOOLE_PROCESS;

    protected $serverType='tcp';

    protected $option=[
        'worker_num '=>4,
        'task_worker_num'=>2,
        'daemonize'=>true,
        // 监听队列的长度
        'backlog'    => 128,
        //启用CPU亲和设置
        'open_cpu_affinity' => 1,
        'log_file' => '/data/log/swoole.log',
        //worker进程数据包分配模式
        //1平均分配，2按FD取模固定分配，3抢占式分配，默认为取模(dispatch=2)
        'dispatch_mode '=>3
    ];

    public function onWorkerStart()
    {
        $redis=new \Redis();
        $redis->connect('127.0.0.1',6379);
        swoole_timer_tick(2000,function ()use ($redis){
            $time=time()-15*60;
//            $data=$redis->zRangeByScore('order_store_log',$time,$time+60);
            $data=$redis->rawCommand('ZRANGEBYSCORE','order_store_log', $time, $time+60);
            if (!empty($data)){
                foreach ($data as $k=>$v){
                    //检测订单是否已经支付
                    $check=Db::name('mall_product_order')->where('id',$v)->value('status');
                    $info=0;
                    if ($check==1){
                        //如果已经支付，执行库存历史记录--确认成功，
                        $info=MallStoreLog::update(['status'=>1],['order_id'=>$v]);
                    }elseif($check==0){
                        //如果超过了15分钟还没支付成功，则这笔订单需要取消，扣减库存也得归还
                        $info=MallStoreLog::update(['status'=>-1,'remark'=>date('Y-m-d H:i:s',time()).'退还库存'],['order_id'=>$v]);
                        if ($info){
                            $log_info=\db('mall_store_log')->find(['order_id'=>$v]);
                            \db('mall_store_attr')
                                ->where(['pid'=>$log_info['store_id'],'title'=>$log_info['store_title']])
                                ->setInc('store',$log_info['num']);
                        }
                    }
                    if ($info){
                        $redis->zDelete('order_store_log',$v);
                    }
                }
            }
        });
    }

    public function onReceive($server, $fd, $from_id, $data)
    {
//        $server->send($fd, 'Swoole: '.$data);

        $task_id = $this->swoole->task($data);
        echo "开始投递异步任务 id=$task_id\n";
    }

    public function onTask($server, $task_id, $from_id, $data)
    {
         echo "接收异步任务[id=$task_id]".PHP_EOL;
         $data=json_decode($data,true);
         if (!empty($data['input_file'])){
             $input_file=$data['input_file'];
             $tmp=strtr($input_file,['.mp4'=>'']);
             $out_file=$tmp.'_min.mp4';
             $log_file='./video_log/'.md5($input_file).'.log';

             if (!file_exists($out_file)){
                 $process=new Process(function (\swoole_process $worker)use($input_file,$out_file,$log_file){
                     $worker->exec('/bin/sh',['/home/wwwroot/voyage_music/public/deal_video.sh',$input_file,$out_file,$log_file]);
                 },true);
                 $process->start();
                 if($ret=\swoole_process::wait(true)){
                     echo "PID={$ret['pid']} out\n";
                     //上传到阿里云oss
                     $save_name=ltrim($out_file,'./');
                     $file_type=mime_content_type($out_file);
                     $source_file=$out_file;
                     $result=OSS::privateUpload('voyage',$save_name,$source_file,['ContentType'=>$file_type]);
                     $video_url=OSS::getPublicObjectURL('voyage',$save_name);
                     if (!empty($data['table_info'])){
                         $info=Db::name($data['table_info']['table'],[],true)
                             ->where($data['table_info']['map'])
                             ->update([
                                 $data['table_info']['field']=>$video_url
                             ]);
                         if ($info){
                             VideoLog::update(['status'=>1,'log_file'=>$log_file],['id'=>$data['log_id']]);
                         }
                     }
                 }else{
                     $video_url=OSS::getPublicObjectURL('voyage',ltrim($out_file,'./'));
                     $info=Db::name($data['table_info']['table'],[],true)
                         ->where($data['table_info']['map'])
                         ->update([
                             $data['table_info']['field']=>$video_url
                         ]);
                     if ($info){
                         VideoLog::update(['status'=>1,'log_file'=>$log_file,'remote_path'=>$video_url,'remark'=>'已经存在该文件'],['id'=>$data['log_id']]);
                     }
                     $this->swoole->finish('');
                 }
             }

         }
    }

    public function onFinish($server, $task_id, $data)
    {
        echo "异步任务[id=$task_id]完成".PHP_EOL;
    }

    //注册各种信号
    private function signal(){
        // 注册信号，回收退出的子进程
        \swoole_process::signal(SIGCHLD, function($sig) {
            while($ret =  \swoole_process::wait(false)) {
                echo "PID={$ret['pid']} out\n";
            }
        });
    }
}