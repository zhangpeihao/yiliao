<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/23
 * Time: 13:37
 */
namespace app\common\model;

use app\common\library\Auth;
use think\Model;

class MallStore extends Model
{
    protected $name='mall_store';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=['category_text','product_attr'];

    protected static function init()
    {
        MallStore::event('before_insert',function ($store){
            $auth=Auth::instance();
            $store->agency_id=$auth->agency_id;
        });
    }

    public function getStoreAttr($value,$data)
    {
        return db('mall_store_attr')->where('pid',$data['id'])->sum('store');
    }

    public function getProductAttrAttr($value,$data)
    {
        $res=\model('mall_store_attr')->where('pid',$data['id'])->select();
        if (empty($res)){
            return [];
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