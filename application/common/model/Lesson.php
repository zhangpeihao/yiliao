<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/3 14:48
 */
namespace app\common\model;

use think\Model;

class Lesson extends Model{
    protected $table='__LESSON__';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=['category_info','status_text'];

    public function getCreatorAttr($value)
    {
        return (string)db('user')->where('id',$value)->value('username');
    }

    public function getStatusTextAttr($value,$data)
    {
        $list=[0=>'禁用',1=>'正常'];
        return $list[$data['status']];
    }

    public function getCategoryInfoAttr($value,$data)
    {
        $res=model('mall_category')->where('id',$data['cid'])->find();
        if ($res){
            return $res->toArray();
        }else{
            return (object)[];
        }
    }

    public function getCoverAttr($value,$data)
    {
        if (empty($value)){
            return request()->domain().'/assets/img/timg.jpg';
        }else{
            if (!strstr($value,'http')){
                return request()->domain().$value;
            }else{
                return $value;
            }
        }
    }

}