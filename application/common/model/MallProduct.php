<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/24
 * Time: 15:56
 */
namespace app\common\model;

use think\Model;

class MallProduct extends Model{
    protected $name='mall_product';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=['category_text','product_menu','lesson_info','product_attr','store_attr'];

    protected $store_attr;

    protected $product_attr;

    public function getStoreAttr($value,$data)
    {
        $attr=db('mall_product_attr')->where('pid',$data['id'])->value('attr_value');
        $attr=parse_attr($attr);
        //商品规格
        foreach ($attr as $k=>$v){
            $res[]=['key'=>$v, 'value'=>$v];
        }
        $this->product_attr=$res;

        if (empty($attr)){return 0;}
        $store=db('mall_store_attr')->where('pid',$data['store_id'])->select();
        $store_count=array_sum(array_column($store,'store'));
        //库存规格
        $this->store_attr=$store;

        return $store_count;
    }

    public function getStoreAttrAttr($value,$data)
    {
        if (empty($this->store_attr)){
            $store=db('mall_store_attr')->where('pid',$data['store_id'])->select();
            return $store;
        }
        return $this->store_attr;
    }

    public function getProductAttrAttr($value,$data)
    {
        if (empty($this->product_attr)){
            $attr=db('mall_product_attr')->where('pid',$data['id'])->value('attr_value');
            $attr=parse_attr($attr);
            //商品规格
            foreach ($attr as $k=>$v){
                $res[]=['key'=>$v, 'value'=>$v];
            }
            return $res;
        }
        return $this->product_attr;
    }

    public function getLessonInfoAttr($value,$data)
    {
        if ($data['lesson_id']){
            $res=\model('Lesson')->where('id',$data['lesson_id'])->find();
            if ($res){
                return  $res->toArray();
            }
        }
        return (object)[];
    }


    public function getProductMenuAttr($value,$data)
    {
        $res=$data=model('MallProductMenu')
            ->field('id,pid,title,price,credit')
            ->where('pid',$data['id'])->where('status',1)->order('id desc')->select();
        return $res;
    }

    public function getContentAttr($value,$data)
    {
        if (!empty($value)){
            return strtr($value,['src="/uploads'=>'src="'.request()->domain().'/uploads']);
        }else{
            return "";
        }
    }

    public function getSelfAcceptAttr($value,$data)
    {
        $res=[];
        if ($value){
            $value=parse_attr($value);
            foreach ($value as &$item){
                $res[]=[
                    'title'=>$item
                ];
            }
        }
        return $res;
    }

    public function getCategoryTextAttr($value,$data)
    {
        return (string)db('mall_category')->where('id',$data['cid'])->value('name');
    }

    public function getLogoAttr($value,$data)
    {
        if (!strstr($value,'http')){
            return request()->domain().$value;
        }else{
            return $value;
        }
    }

    public function getPicsAttr($value,$data)
    {
        if ($value){
            $value=explode(',',rtrim($value,','));
            foreach ($value as &$item){
                if (!strstr($item,'http')){
                    $item=request()->domain().$item;
                }
            }
            return $value;
        }else{
            return [];
        }
    }
}