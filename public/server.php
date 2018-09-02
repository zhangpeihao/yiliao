<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/10
 * Time: 9:42
 */
$serv=new swoole_server("0.0.0.0",9501);

$serv->set([
    "worker_num"=>2
]);

$serv->on('receive',function ($serv,$task_id,$from_id,$data){
    $process=new swoole_process("call_back");
    $pid=$process->start();
    while ($ret=swoole_process::wait(true)){
        echo "PID={$ret['pid']}\n";
    }
    echo "New AsyncTask[id=$task_id]".PHP_EOL;
});

//处理异步任务的结果
$serv->on('finish',function ($serv,$task_id,$data){
    echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
});

function call_back(swoole_process $worker)
{
    swoole_set_process_name('child_sync');
    $pid=$worker->pid;

    $worker->exec("ffmpeg",['']);
}