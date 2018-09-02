<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/26
 * Time: 20:08
 */
namespace app\common\model;

use think\Model;

class MallStoreLog extends Model
{
    protected $name='mall_store_log';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    public static function add_store_log($order_id,$status=0,$out_trade_no="")
    {
        $info=0;
        if ($out_trade_no){
            $order_list=db('mall_product_order')->where('out_trade_no',$out_trade_no)->field('id,uid,num,pid,attr')->select();
            if (empty($order_list)){return false;}
            foreach ($order_list as $order_info){
                $store_id=db('mall_product')->where('id',$order_info['pid'])->value('store_id');
                $store=db('mall_store_attr')->where([
                    'pid'=>$store_id,
                    'title'=>$order_info['attr']
                ])->value('store');
                $data=[
                    'order_id'=>$order_info['id'],
                    'uid'=>$order_info['uid'],
                    'num'=>$order_info['num'],
                    'pid'=>$order_info['pid'],
                    'last_num'=>$store,
                    'store_id'=>$store_id,
                    'store_title'=>$order_info['attr'],
                    'status'=>$status
                ];
                $info=MallStoreLog::create($data,true);
                db('mall_store_attr')->where(['pid'=>$store_id,'title'=>$data['store_title']])->setDec('store',$data['num']);
                //投递订单是否超时任务
                try{
                    $redis=new \Redis();
                    $redis->connect('127.0.0.1',6379);
                    $redis->zAdd('order_store_log',time(),$order_info['id']);
                    $redis->close();
                }catch (\Exception $e){}
            }
        }else{
            $order_info=db('mall_product_order')->where('id',$order_id)->field('id,uid,num,pid,attr')->find();
            $store_id=db('mall_product')->where('id',$order_info['pid'])->value('store_id');
            $store=db('mall_store_attr')->where([
                'pid'=>$store_id,
                'title'=>$order_info['attr']
            ])->value('store');
            $data=[
                'order_id'=>$order_id,
                'uid'=>$order_info['uid'],
                'num'=>$order_info['num'],
                'pid'=>$order_info['pid'],
                'last_num'=>$store,
                'store_id'=>$store_id,
                'store_title'=>$order_info['attr'],
                'status'=>$status
            ];
            $info=MallStoreLog::create($data,true);
            db('mall_store_attr')->where(['pid'=>$store_id,'title'=>$data['store_title']])->setDec('store',$data['num']);
            //投递订单是否超时任务
            try{
                $redis=new \Redis();
                $redis->connect('127.0.0.1',6379);
                $redis->zAdd('order_store_log',time(),$order_id);
                $redis->close();
            }catch (\Exception $e){}
        }
        if ($info){
            return true;
        }else{
            return false;
        }

    }
}