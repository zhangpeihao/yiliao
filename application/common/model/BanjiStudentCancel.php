<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/11 16:24
 */
namespace app\common\model;

use think\Model;

class BanjiStudentCancel extends Model{
    protected $name='banji_student_cancel';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    public static function getStudent_list($shedule_id)
    {
        return self::where(['shedule_id'=>$shedule_id])->column('student_id');
    }

    public static function clear_student_cancel($shedule_id,$students=[])
    {
        if ($students){
            BanjiStudentCancel::destroy(['shedule_id'=>$shedule_id,'student_id'=>['in',$students]]);
        }
        return true;
    }
}