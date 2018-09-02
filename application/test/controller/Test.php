<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/6
 * Time: 15:39
 */
namespace app\test\controller;

use MongoDB\Driver\Manager;
use think\Collection;
use think\Controller;
use util\OSS;

class Test extends Controller{
    protected $mongoManager;
    protected $mongoCollection;
    public function __construct()
    {
//        $this->mongoManager = new Manager($this->getUri());
//        $this->mongoCollection = new Collection($this->mongoManager, "testdb","test");
    }
    public function test()
    {
        // 读取一条数据
        $data = $this->mongoCollection->all();
        dump($data);
    }
    protected function getUri()
    {
        return getenv('MONGODB_URI') ?: 'mongodb://192.168.32.128:27017';
    }

    public function update_video()
    {
        $data=db('topic_post')->where('video','neq','')->select();
        foreach ($data as $datum) {
            $str='https://music.588net.com';
            if (strstr($datum['video'],$str)){
                $save_name=strtr($datum['video'],[$str=>'']);
                $path='.'.$save_name;
                $file_type=mime_content_type($path);
                $save_name=ltrim($save_name,'/');
                dump($file_type);
                dump($save_name);
                dump($path);
                dump(file_exists($path));
//                exit();
                $result=OSS::privateUpload('voyage',$save_name,$path,['ContentType'=>$file_type]);
//                dump($result);exit();
                $file_url=OSS::getPublicObjectURL('voyage',$save_name);
//                dump($file_path);
//                $file_path=OSS::getPrivateObjectURLWithExpireTime('voyage',$save_name,new \DateTime('+1 day'));
//                dump($file_path);
//                exit();
                db('topic_post')->where('id',$datum['id'])->update(['video'=>$file_url]);
                dump(db()->getLastSql());
            }
        }
    }

    public function add_test()
    {
        $sh = scws_open();
        scws_set_charset($sh, 'utf8');
        scws_set_dict($sh, '/usr/local/scws/etc/dict.utf8.xdb');
        scws_set_rule($sh, '/usr/local/scws/etc/rules.ini');
        $text = "我是一个中国人，我会C++语言，我也有很多T恤衣服";
        scws_send_text($sh, $text);
        $top = scws_get_tops($sh, 5);
        print_r($top);
    }
}