<?php

namespace app\common\model;

use think\Model;

class MallProductAttr extends Model
{
    // 表名
    protected $name = 'mall_product_attr';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'product_title','attr_value'
    ];

    public function getProductTitleAttr($value,$data)
    {
        return (string)db('mall_product')->where('id',$data['pid'])->value('title');
    }

    public function getAttrValueAttr($value,$data)
    {
        if (empty($value)){
            return [];
        }else{
            $tmp=parse_attr($value);
            $res=[];
            foreach ($tmp as $k=>$v){
                $res[]=[
//                    'key'=>$k,
                    'key'=>$v,
                    'value'=>$v
                ];
            }
            return $res;
        }
    }
}
