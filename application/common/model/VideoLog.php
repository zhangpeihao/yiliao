<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/11
 * Time: 17:40
 */
namespace app\common\model;

use think\Model;

class VideoLog extends Model
{
    protected $name='video_log';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';


    public static function add_video_task($table="",$map=[],$field="",$path="")
    {
        if (strstr($path,'min')){return false;}
        if (strstr($path,'http')){
            if (strstr($path,config('img_domain')) || strstr($path,config('oss_url'))){
                $path=strtr($path,[config('img_domain')=>'',config('oss_url')=>'']);
            }else{
                return false;
            }
        }
        $video_log=new VideoLog();
        $video_log->save([
            'table'=>$table,'map'=>json_encode($map),'field'=>$field,'video_path'=>$path
        ]);
        $log_id=$video_log->id;
        $client = new \Swoole\Client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_SYNC);
        $client->connect('127.0.0.1',9510);
        $client->send(json_encode([
//            'input_file'=>'./uploads/20180705/ba31058c333609ef00179fa10d73e108.mp4'
            'input_file'=>'.'.$path,
            'table_info'=>[
                'table'=>$table,
                'map'=>$map,
                'field'=>$field
            ],
            'log_id'=>$log_id
        ]));
        $client->close();
    }
}