<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/26 16:10
 */
namespace app\common\model;

use think\Model;

class PracticeComment extends Model{

    protected $name='practice_comment';

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

    public function getCreatetimeAttr($value,$data)
    {
        return formatTime($value);
    }

    public function getUpdatetimeAttr($value,$data)
    {
        return formatTime($value);
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
       /* if ($data['bid']){
            $res=PracticeComment::find($data['bid']);
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
            $res=model('User')->field('id,username,mobile,avatar')->find($data['uid']);
            if ($res){
                return $res->toArray();
            }else{
                return (object)[];
            }
        }else{
            return (object)[];
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

    public function getContentAttr($value,$data)
    {
        if ($data['bid']){
            $parent_comment=\model('practice_comment')->where('id',$data['bid'])->find();
            if ($parent_comment['bid']){
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