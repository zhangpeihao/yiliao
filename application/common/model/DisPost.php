<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/5/4 14:59
 */
namespace app\common\model;

use think\Model;

class DisPost extends Model{
    protected $name='dis_post';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    protected $append=[
        'user','type_text'
    ];


    public function setCoverAttr($value,$data)
    {
        $cover='';
        if ($data['video']){
            $video=strtr($data['video'],[config('img_domain')=>'','http://voyage.oss-cn-beijing.aliyuncs.com'=>'']);
            $cover=create_video_thumb($video,10);
        }
        return $cover;
    }

   /* public function getCoverAttr($value,$data)
    {
        if (strstr($data['video'],'oss')){
            return $data['video'].'?x-oss-process=video/snapshot,t_7000,f_jpg,w_0,h_0,m_fast';
        }else{
            return $value;
        }
    }*/

    public function getTypeTextAttr($value,$data)
    {
        $list=[0=>'公开',1=>'仅限老师'];
        return $list[$data['type']];
    }

    public function getUserAttr($value,$data)
    {
        $res=model('User')->field('id,username,avatar,nickname,group_id')->find($data['uid']);
        if ($res){
            return $res->toArray();
        }else{
            return [];
        }
    }

    public function getPicsAttr($value,$data)
    {
        if (empty($value)){
            return [];
        }
        return explode(',',trim($value));
    }

    public function getCtimeAttr($value,$data)
    {
        return formatTime($value);
    }

    public function getUtimeAttr($value,$data)
    {
        return formatTime($value);
    }
}