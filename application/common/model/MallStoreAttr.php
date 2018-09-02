<?php

namespace app\common\model;

use think\Model;

class MallStoreAttr extends Model
{
    // 表名
    protected $name = 'mall_store_attr';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;

    public static function update_store($store_id,$title,$num)
    {
        return db('mall_store_attr')->where('title',$title)->where('pid',$store_id)->dec('store',$num)->update();
    }

}
