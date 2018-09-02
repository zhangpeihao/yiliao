<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/24
 * Time: 18:53
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 商城购物车
 * Class MallCart
 * @package app\api\controller
 */
class MallCart extends Api{
    protected $noNeedLogin=[];

    protected $noNeedRight='*';

    /**
     * 获取我的购物车记录
     * @ApiMethod   (GET)
     * @ApiParams   (name="", type="", required=true, description="")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1527562172','data':[{'id':190,'pid':1,'cid':2,'uid':11,'num':4,'price':'20.00','credit':100,'discount':'0.00','attr':[{'attr_id':1,'attr_title':'产品规格','attr_value':1}],'post_type':0,'self_accept':'','status':1,'ctime':1527561974,'utime':1527561992,'product_info':{'id':1,'agency_id':1,'cid':2,'title':'尤克里里','summary':'','logo':'http:\/\/music.test.com\/uploads\/20180524\/7082dc1097085bd5133a73386653ae7f.png','pics':['http:\/\/music.test.com\/uploads\/20180524\/7082dc1097085bd5133a73386653ae7f.png'],'content':'<p>简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介<\/p>','post_fee':'0.00','attr_id':'','extra_attr':'','lat':'','lng':'','address':'','price':'20.00','origin_price':'0.00','credit':100,'store':100,'sale_num':0,'limit':0,'transfer':'','sale_total':0,'begin_time':1527151907,'end_time':1527151907,'is_recommend':0,'status':1,'sort':0,'remark':'','kefu':'','ctime':'1970-01-01 08:00','utime':'1970-01-01 08:00','category_text':'尤克里里','product_menu':[{'id':1,'pid':1,'title':'套餐一','price':'100.00','credit':'0.00'}]}}]}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 19:16
     */
    public function get_list()
    {
        $map=[];
        $map['status']=1;
        $map['uid']=$this->auth->id;
        $map['agency_id']=$this->auth->agency_id;
        $data=model('MallCart')->where($map)->order('id desc')->select();
        foreach ($data as &$item){
            $item['attr']=json_decode($item['attr'],true);
        }
        $this->success('查询成功',$data);
    }

    /**
     * 添加到购物车
     * @ApiMethod   (POST)
     * @ApiParams   (name="pid", type="int", required=true, description="商品id")
     * @ApiParams   (name="num", type="int", required=true, description="数量")
     * @ApiParams   (name="attr", type="json", required=true, description="属性json字符串：格式：attr_id属性id,attr_title属性名称，attr_value属性值，多个按二维数组拼接，如：[{\"attr_id\":1,\"attr_title\":\"\u4ea7\u54c1\u89c4\u683c\",\"attr_value\":1}]")
     * @ApiReturn (data="{'code':1,'msg':'加入成功','time':'1527561992','data':[]}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 19:04
     */
    public function add_cart()
    {
        $uid=$this->auth->id;
        $pid=request()->request('pid',0,'intval');
        $num=request()->request('num',0,'intval');
        $attr=request()->request('attr','','strval');
//        $post_type=request()->request('post_type',1,'intval');//0自提 1邮寄
//        $self_accept=request()->request('self_accept','','strval');//自提地点

        $check=db('mall_product_attr')->where(['pid'=>$pid])->find();
        $attr_arr=json_decode($attr,true);
        if (empty($attr_arr) && !empty($check)){
            $this->error('请选择属性类型');
        }
        $product=db('mall_product')->find($pid);
        if ($product['status']==0){
            $this->error('该商品还未上架');
        }
        $store_title=$attr_arr[0]['attr_value'];
        $store=db('mall_store_attr')->where('pid',$product['store_id'])->where('title',$store_title)->value('store');
        $product['rest_store']=$store-$product['sale_num'];
        $user_cart_num=db('mall_cart')->where('uid',$uid)->where('pid',$pid)->where('status',1)->count();
        if ($product['rest_store']<($num+$user_cart_num)){
            $this->error('库存不足，加入失败，请稍后尝试');
        }
        if ($product['limit']>0 && $product['limit']<($user_cart_num+$num)){
            $this->error('加入失败！该商品每人限购'.$product['limit'].'件');
        }
        $info=\app\common\model\MallCart::add_cart($uid,$pid,$num,$attr);
        if ($info){
            $this->success('加入成功');
        }else{
            $this->error('加入失败'.model('Cart')->getError());
        }
    }


    /**
     * 增加购物车
     * @ApiMethod   (POST)
     * @ApiParams   (name="cart_id", type="int", required=true, description="购物车记录id")
     * @ApiReturnParams   (name="num", type="int", required=true, description="新增数量")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 19:13
     */
    public function inc_cart()
    {
        $uid=$this->auth->id;
        $cart_id=request()->request('cart_id',0,'intval');
        $num=request()->request('num',1,'intval');
        $cart_info=db('mall_cart')->where('id',$cart_id)->field('pid,attr')->find();
        $pid=$cart_info['pid'];
        $attr=$cart_info['attr'];
        $product=db('mall_product')->where('id','eq',$pid)->find();
        if ($product['status']==0){
            $this->error('该商品还未上架或已下架');
        }
        $attr=json_decode($attr,true);
        $store_title=$attr[0]['attr_value'];
        $store=db('mall_store_attr')->where('pid',$product['store_id'])->where('title',$store_title)->value('store');

        $product['rest_store']=$store-$product['sale_num'];
        $user_cart_num=db('mall_cart')->where('uid',$uid)->where('pid',$pid)->where('status',1)->value('num');
        if ($product['rest_store']<($num+$user_cart_num)){
            $this->error('库存不足，加入失败，请稍后尝试');
        }
        if ($product['limit']>0 && $product['limit']<($user_cart_num+$num)){
            $this->error('加入失败！该商品每人限购'.$product['limit'].'件');
        }
        $info=\app\common\model\MallCart::inc_cart($uid,$cart_id,$num);
        if ($info){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 减少购物车
     * @ApiMethod   (POST)
     * @ApiParams   (name="cart_id", type="int", required=true, description="购物车记录id")
     * @ApiReturnParams   (name="num", type="int", required=true, description="减少数量，如果直接删除，传当前数值")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 19:13
     */
    public function remove_cart()
    {
        $uid=$this->auth->id;
        $pid=request()->request('cart_id',0,'intval');
        $num=request()->request('num',1,'intval');
        $info=\app\common\model\MallCart::remove_cart($uid,$pid,$num);
        if ($info){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 批量删除购物车
     * @ApiMethod   (POST)
     * @ApiParams   (name="cart_ids", type="string", required=true, description="购物车id，多个用英文逗号拼接")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/22 20:21
     */
    public function delete_cart()
    {
        $uid=$this->auth->id;
        $cart_ids=request()->request('cart_ids',"",'strval');
        $check=model('MallCart')->where('id','in',$cart_ids)->where('uid',$uid)->count();
        if ($check!=count(explode(',',$cart_ids))){
            $this->error('无权限删除');
        }else{
            $info=\app\common\model\MallCart::destroy(['id'=>['in',$cart_ids]]);
            if ($info){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
    }
}