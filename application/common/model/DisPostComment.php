<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/5/4 16:14
 */
namespace app\common\model;

use think\Model;

class DisPostComment extends Model{
    protected $name='dis_post_comment';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    protected $append=['buser','user','bcomment'];

    public static function init()
    {
        DisPostComment::event('after_insert',function($comment){
            model('DisPost')->where('id',$comment['pid'])->setInc('pcounts');
            if ($comment['bid']>0){
                DisPostComment::where('id',$comment['bid'])->setInc('pcounts');
            }
        });
    }

    public function getBuserAttr($value,$data)
    {
        if ($data['buid']){
            return  model('User')->field('id,username,mobile,avatar')->find($data['buid']);
        }else{
            return [];
        }
    }

    public function getBcommentAttr($value,$data)
    {
        /*if ($data['bid']){
            $res=TopicPostComment::find($data['bid']);
            if ($res){
                return $res->toArray();
            }else{
                return (object)[];
            }
        }else{
            return (object)[];
        }*/
        return (object)[];
    }

    public function getUserAttr($value,$data)
    {
        if ($data['uid']){
            return model('User')->field('id,username,mobile,avatar')->find($data['uid']);
        }else{
            return [
                'id'=>0,
                'username'=>$data['locate'].'网友',
                'mobile'=>"",
                'avatar'=>config('img_domain').'/assets/img/avatar.png'
            ];
        }
    }

    //评论多图
    public function getPicsAttr($value,$data)
    {
        if ($value){
            $value=explode(',',$value);
            return $value;
        }else{
            return [];
        }
    }


    public function getCtimeAttr($value,$data)
    {
        return formatTime($value);
    }

    public function getUtimeAttr($value,$data)
    {
        return formatTime($value);
    }

    public function getContentAttr($value,$data)
    {
        if ($data['bid']){
            $parent_comment=\model('dis_post_comment')->where('id',$data['bid'])->find();
            if ($parent_comment['pbid']){
                $parent_string=$parent_comment['user']['username'].'：'.$parent_comment['content'];
                return $value.'//@'.$parent_string;
            }else{
                return $value;
            }
        }else{
            return $value;
        }
    }
}