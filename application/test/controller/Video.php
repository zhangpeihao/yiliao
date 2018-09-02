<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/8
 * Time: 12:06
 */
namespace app\test\controller;

use Swoole\Process;
use think\Controller;

class Video extends Controller
{
    public function index()
    {
        echo date('Y-m-d H:i:s',time());
    }


    public function deal_video($input_file="./uploads/20180705/ba31058c333609ef00179fa10d73e108.mp4")
    {
        set_time_limit(0);
        $tmp=strtr($input_file,['.mp4'=>'']);
        $out_file=$tmp.'_min.mp4';
        $log_file=md5($input_file).'.log';
//        $cmd = 'sudo docker run -v=`pwd`:/tmp/ffmpeg opencoconut/ffmpeg  -i  '.$input_file.' -c:v libx264 -strict -2 '.$out_file.' -y 1>block.txt 2>&1';
        $cmd = 'ffmpeg  -i  '.$input_file.' -c:v libx264 -strict -2 '.$out_file.' -y 1>'.$log_file.' 2>&1';
        echo  $cmd;
//        system($cmd,$res);
//        pclose(popen($cmd,'r'));
//        dump($res);
        $process=new Process(function (Process $childProcess){
           $childProcess->exec('/usr/bin/php',['/home/wwwroot/']);
        });
    }

    public function get_con($log_file='block.txt')
    {
        $content = @file_get_contents($log_file);
        if($content){
            //get duration of source
            preg_match("/Duration: (.*?), start:/", $content, $matches);
            $rawDuration = $matches[1];
            //rawDuration is in 00:00:00.00 format. This converts it to seconds.
            $ar = array_reverse(explode(":", $rawDuration));
            $duration = floatval($ar[0]);
            if (!empty($ar[1])) $duration += intval($ar[1]) * 60;
            if (!empty($ar[2])) $duration += intval($ar[2]) * 60 * 60;
            //get the time in the file that is already encoded
            preg_match_all("/time=(.*?) bitrate/", $content, $matches);
            $rawTime = array_pop($matches);
            //this is needed if there is more than one match
            if (is_array($rawTime)){
                $rawTime = array_pop($rawTime);
            }}
        //rawTime is in 00:00:00.00 format. This converts it to seconds.
        $ar = array_reverse(explode(":", $rawTime));
        $time = floatval($ar[0]);
        if (!empty($ar[1])) $time += intval($ar[1]) * 60;
        if (!empty($ar[2])) $time += intval($ar[2]) * 60 * 60;
        //calculate the progress
        $progress = round(($time/$duration) * 100);
        echo "Duration: <label id='du'>" . $duration . "</label><br>";
        echo "Current Time: " . $time . "<br>";
        echo "Progress: <label id='progress'>" . $progress . "</label>%";
        echo "<script>_interval=setInterval(function() {
                            var pro=document.getElementById('progress');
                            var pro_text=pro.innerText;
                            console.log(pro_text);
                            if (pro_text!='100'){
                                window.location.reload();
                            } else {
                                alert('转码完毕');
                                clearInterval(_interval);
                            }
                        },2000)</script>";
    }
}