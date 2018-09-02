<?php

namespace app\admin\model;

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
        'product_title'
    ];

    public function getProductTitleAttr($value,$data)
    {
        return (string)db('mall_product')->where('id',$data['pid'])->value('title');
    }

    







}
