<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/26 16:05
 */
namespace app\common\model;

use think\Model;

class PracticeAdmire extends Model{
    protected $name='practice_admire';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    public static function is_admire($pid,$cid=0,$uid)
    {
        if (self::where(['pid'=>$pid,'cid'=>$cid,'uid'=>$uid])->find()){
            return 1;
        }else{
            return 0;
        }
    }

    public static function add_admire($pid,$cid=0,$uid)
    {
       return self::insertGetId([
            'pid'=>$pid,
            'cid'=>$cid,
            'uid'=>$uid,
        ]);
    }

    public static function cancel_admire($pid,$cid=0,$uid)
    {
        return self::where([
            'pid'=>$pid,'cid'=>$cid,'uid'=>$uid
        ])->delete();
    }
}