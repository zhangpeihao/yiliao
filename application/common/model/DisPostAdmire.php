<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/26 16:05
 */
namespace app\common\model;

use think\Model;

class DisPostAdmire extends Model{
    protected $name='dis_post_admire';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    public static function is_admire($pid,$cid=0,$uid)
    {
        if (empty($uid)){
            $map=['pid'=>$pid,'cid'=>$cid,'ip'=>request()->ip(0)];
        }else{
            $map=['pid'=>$pid,'cid'=>$cid,'uid'=>$uid];
        }
        if (self::where($map)->find()){
            return 1;
        }else{
            return 0;
        }
    }

    public static function add_admire($pid,$cid=0,$uid)
    {
       if ($cid==0){
           \model('DisPost')->where(['id'=>$pid])->setInc('likecount');
       }else{
           \model('DisPostComment')->where(['pid'=>$pid,'id'=>$cid])->setInc('likecount');
       }
        $ip=request()->ip(0);
        $res=GetIpLookup($ip);
        $locate=explode('|',$res['address']);
        return self::insertGetId([
            'pid'=>$pid,
            'cid'=>$cid,
            'uid'=>$uid,
            'ip'=>$ip,
            'locate'=>$locate
        ]);
    }

    public static function cancel_admire($pid,$cid=0,$uid)
    {
        $info=self::where([
            'pid'=>$pid,'cid'=>$cid,'uid'=>$uid
        ])->delete();
        if ($info){
            if ($cid==0){
                \model('DisPost')->where(['id'=>$pid])->setDec('likecount');
            }else{
                \model('DisPostComment')->where(['pid'=>$pid,'id'=>$cid])->setDec('likecount');
            }
        }
        return $info;
    }

    public function getCreatetimeAttr($value,$data)
    {
        return formatTime($value);
    }

    public function getUpdatetimeAttr($value,$data)
    {
        return formatTime($value);
    }
}