<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/24
 * Time: 18:02
 */
namespace app\common\model;

use think\Model;

class MallProductComment extends Model{
    protected $name='mall_product_comment';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';


    protected $append=['buser','user','bcomment'];

    public static function init()
    {
        PracticeComment::event('after_insert',function($comment){
            model('Practice')->where('id',$comment['pid'])->setInc('pcounts');
        });
    }

    public function getBuserAttr($value,$data)
    {
        if ($data['buid']){
            $res=model('User')->field('id,username,mobile,avatar')->find($data['buid']);
            if ($res){
                return $res->toArray();
            }else{
                return (object)[];
            }
        }else{
            return (object)[];
        }
    }

    public function getBcommentAttr($value,$data)
    {
        if ($data['bid']){
            $res=MallProductComment::find($data['bid']);
            if ($res){
                return $res->toArray();
            }else{
                return [];
            }
        }else{
            return [];
        }
    }

    public function getUserAttr($value,$data)
    {
        if ($data['uid']){
            $res=model('User')->field('id,username,mobile,avatar')->find($data['uid']);
            if ($res){
                return $res->toArray();
            }else{
                return [];
            }
        }else{
            return [];
        }
    }

    //评论多图
    public function getPicsAttr($value,$data)
    {
        if ($value){
            $value=explode(',',$value);
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