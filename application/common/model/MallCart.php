<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/24
 * Time: 18:54
 */
namespace app\common\model;

use app\common\library\Auth;
use think\Model;

class MallCart extends Model{
    protected $name='mall_cart';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    protected $append=['product_info','attr_info'];

    public function getAttrInfoAttr($value,$data)
    {
        if (empty($data['attr'])){return [];}
        if (is_array($data['attr'])){
            return $data['attr'];
        }
        return json_decode($data['attr'],true);
    }

    public function getProductInfoAttr($value,$data)
    {
        $res=\model('MallProduct')->find($data['pid']);
        if ($res){
            return $res->toArray();
        }else{
            return [];
        }
    }


    public static function add_cart($uid,$pid,$num,$attr)
    {
        $product=db('mall_product')->where(['id'=>$pid])->find();
        $data=[
            'pid'=>$pid,
            'cid'=>$product['cid'],
            'uid'=>$uid,
            'num'=>$num,
            'price'=>$product['price'],
            'credit'=>$product['credit'],
            'status'=>1,
            'ctime'=>time(),
            'attr'=>$attr,
        ];
        $check=db('mall_cart')->where(['pid'=>$pid,'uid'=>$uid,'attr'=>$attr])->where('status',1)->find();
        if ($check){
            $info=db('mall_cart')->where('id',$check['id'])->inc('num',$num)->update(['utime'=>time()]);
        }else{
            $auth=Auth::instance();
            $data['agency_id']=$auth->agency_id;
            $info=db('mall_cart')->insertGetId($data);
        }
        if ($info){
            return true;
        }else{
            return false;
        }
    }



    public static function remove_cart($uid,$cart_id,$num)
    {

        $cart=db('mall_cart')->where(['id'=>$cart_id,'uid'=>$uid])->find();
        if ($cart['num']==$num){
            $info=db('mall_cart')->where('id',$cart['id'])->delete();
        }else{
            $info=db('mall_cart')->where('id',$cart['id'])->setDec('num',$num);
        }
        if ($info){
            return true;
        }else{
            return false;
        }
    }
    public static function inc_cart($uid,$cart_id,$num)
    {

        $cart=db('mall_cart')->where(['id'=>$cart_id,'uid'=>$uid])->find();
        if ($cart){
            $info=db('mall_cart')->where('id',$cart['id'])->setInc('num',$num);
            if ($info){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}