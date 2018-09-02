<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/7
 * Time: 11:56
 */
namespace app\test\controller;

use app\test\model\Place;
use app\test\model\User;
use MongoDB\BSON\ObjectId;
use think\Controller;
use think\Db;

class Mon extends Controller{
    public function index()
    {
//        dump(new ObjectId());
        $res=model('User')->paginate(5);
        $query=[];
//        model('User')->query($query);
//        dump($res);
        return json($res);
    }


    public function geo()
    {
        $data=model('Place')->where('loc','near',[
            '$geometry'=>[
                'type'=>'Point',
                'coordinates'=>[117.161609,39.080704]
            ],
            '$maxDistance' => 300,
        ])->paginate(10);
//        dump(model('Place')->getLastSql());
//        dump((array)$data);
//        dump(getDistance([-73.97, 40.77],[-73.88, 40.78]));
//        dump(getDistance([-73.969999999999999,40.770000000000003],[-73.879999999999995,40.780000000000001]));
//        exit();
        return json($data);
    }

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

    public function create_data()
    {
//        $res=model('Place')->whereNull('name')->delete();
//        dump($res);
//        exit();
        foreach ($this->geo as $k=>$v){
            Place::create([
                'loc'=>[
                    'type'=>'Point',
                    'coordinates'=>$v
                ],
                'name'=>$k,
                'category'=>'runway'
            ]);
            dump(model('Place')->getLastSql());
        }
    }


}