<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/10
 * Time: 18:06
 */
namespace app\task\controller;

use app\common\model\VideoLog;
use think\Controller;
use think\Db;
use util\OSS;

class Test extends Controller
{
    public function index()
    {
//        VideoLog::add_video_task('topic_post',['id'=>3],'min_video','http://voyage.oss-cn-beijing.aliyuncs.com/uploads/20180504/d41396454ab80c975755423e8c981b07.mp4');
//        $info=video_info('./uploads/20180504/d41396454ab80c975755423e8c981b07.mp4');
//        dump($info);
//        exit();
        $data=db('topic_post')->where('min_video','eq','')->select();
        foreach ($data as $v){
            $tmp_path=strtr($v['video'],[config('img_domain')=>'',config('oss_url')=>'']);
            $info=video_info('.'.$tmp_path);
            if (!empty($info)){
                \db('topic_post')->where('id',$v['id'])->update([
                    'min_video'=>$v['video'],'wh'=>$info[0]['width'].'x'.$info[0]['height'],'time'=>$info[0]['play_time'],'size'=>$info[0]['size']
                ]);
                if ($info[0]['width']>=720 || $info[0]['height']>=1280){
                    echo "投递了".$v['id']."宽高是：".$info[0]['width'].'x'.$info[0]['height']."\n";
                    VideoLog::add_video_task('topic_post',['id'=>$v['id']],'min_video',$v['video']);
                }
            }
        }
        exit();
    }

    public function upload_pra()
    {
        $data=db('practice')->where('video','like',config('img_domain').'%')->select();
        foreach ($data as $item){
            $video=$item['video'];
            try{
                $str='https://music.588net.com';
                $save_name=strtr($video,[$str=>'']);
                $path='.'.$save_name;
                $file_type=mime_content_type($path);
                $save_name=ltrim($save_name,'/');
                $result=OSS::privateUpload('voyage',$save_name,$path,['ContentType'=>$file_type]);
//                dump($result);exit();
                $data['video']=OSS::getPublicObjectURL('voyage',$save_name);
                db('practice')->where('id',$item['id'])->update(['video'=>$data['video']]);
                dump(db()->getLastSql());
            }catch (\Exception $e){}
        }
    }

    public function test()
    {
        $redis=new \Redis();
        $redis->connect('127.0.0.1',6379);
//        $res=$redis->zRangeByScore('salary',500,3000,['withscores']);
        $res=$redis->rawCommand('ZRANGEBYSCORE','salary', 500, 3000);
        foreach ($res as $v){
            dump($v);
            dump($redis->zScore('salary',$v));

        }
        $redis->zDelete('salary','tom');
    }
}