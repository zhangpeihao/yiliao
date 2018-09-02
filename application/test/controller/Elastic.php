<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/12
 * Time: 12:56
 */
namespace app\test\controller;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Endpoints\Indices\Analyze;
use think\Controller;

class Elastic extends Controller
{
    public function create_index()
    {
        //Elastic search php client
        $client=ClientBuilder::create()->setHosts(['192.168.32.128'])->build();
        $data=db('user')->select();

        //delete index which already created
        $param=[];
        $param['index']='website';
        $client->indices()->delete($param);
        //create index on log_date,src_ip,dest_ip
        foreach ($data as $v){
            $param=[];
            $param['body']=$v;
            $param['index']='website';
            $param['type']='web';

            //Document will be indexed to log_index/log_type/autogenerate_id
            $res=$client->index($param);
            dump($res);
        }
    }

    public function search()
    {
        $client=ClientBuilder::create()->setHosts(['192.168.32.128'])->build();
        $param=[];
        $param['index']='user_index';
        $param['type']='user_type';
        //单个字段查询
//        $param['body']['query']['match']=['username'=>'wx'];
        //单个字段or查询
        $param['body']['query']['match']=['username'=>'余 江'];
        //多字段查询
        /*$param['body']['query']['bool']['must']=[
//            ['match'=>['username'=>'wx']],
            ['wildcard'=>['username'=>'余']]
        ];*/
//        $param['body']['sort']=['id'=>['order'=>'desc']];
        $param['size']=10;
        $param['from']=0;
        $data=$client->search($param);
        dump($data);
    }

    protected $geo=[
        [117.163999,39.082846],
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


    public function add_geo()
    {
        $client=ClientBuilder::create()->setHosts(['192.168.32.128'])->build();
//        $param=[];
//        $param['index']='attractions';
//        $res=$client->indices()->delete($param);
        /*$param['body']=[
            'mappings'=>[
                "restaurant"=>[
                    "properties"=>[
                        "name"=>["type"=>"string"],
                        "location"=>["type"=>'geo_point']
                    ]
                ]
            ]
        ];
        $res=$client->indices()->create($param);
        dump($res);
        exit();*/
        foreach ($this->geo as $k=>$v){
            $param=[];
            $param['index']='attractions';
            $param['type']='restaurant';
            $param['body']=[
                'name'=>strval($k),
                'location'=>["lat"=>$v[1],"lon"=>$v[0]]
            ];
//            dump($param);exit();
            $res=$client->index($param);
            dump($res);
        }
    }

    public function geo_check()
    {
        $client=ClientBuilder::create()->setHosts(['192.168.32.128'])->build();
        $param['index']='attractions';
        $param['type']='restaurant';
        $param['body']['query']=[
            'filtered'=>[
                'filter'=>[
                    //区间查询
                    /*'geo_bounding_box'=>[
                        "location"=>[
                            'top_left'=>["lat"=>39.085157, "lon"=>117.160441],
                            'bottom_right'=>["lat"=>39.080704, "lon"=>117.161609]
                        ]
                    ]*/

                    //距离查询
                    "geo_distance"=>[
                        "distance"=>"300m",
                        "distance_type"=>"arc",
//                        "distance_type"=>"plane",
//                        "distance_type"=>"sloppy_arc",
                        "location"=>[
                            "lat"=>39.080704,"lon"=>117.161609
                        ]
                    ]
                ]
            ]
        ];
        $param['size']=10;
        $param['from']=0;
        $data=$client->search($param);
        dump($data);
    }

    public function word_splice()
    {
        $client=ClientBuilder::create()->setHosts(['192.168.32.128:9200'])->build();
        $params['index'] = 'index';
        $params['body'] = [
//            'analyzer' => 'ik_max_word',
//            'analyzer' => 'ik_smart',
//            'analyzer' => 'pinyin',
            'analyzer' => 'pinyin',
//            'text' => '英雄联盟最强王者'
            'text' => '中华人民共和国国歌'
//            'text' => 'based index manager for Elasticsearch'
        ];
       $res=$client ->indices()->analyze($params);
       dump($res);
    }
}