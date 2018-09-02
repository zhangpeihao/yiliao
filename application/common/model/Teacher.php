<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/3 17:49
 */
namespace app\common\model;

use app\common\library\Auth;
use think\Model;

class Teacher extends Model{
    protected $name='teacher';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    protected $append=['teacher_avatar','type_text'];

    public static function init()
    {
        Teacher::event('before_insert',function ($teacher){
           $auth=Auth::instance();
           $teacher->agency_id=$auth->agency_id;
        });
        Teacher::event('after_insert',function ($teacher){
            AgencyMember::create([
                'type'=>$teacher['type'],
                'agency_id'=>$teacher['agency_id'],
                'teacher_id'=>$teacher['id'],
                'uid'=>0,
                'creator'=>$teacher['creator'],
                'status'=>1
            ],true);
        });
    }

    public function getTeacherAvatarAttr($value,$data)
    {
        if ($data['bind_uid']){
            $user=User::get($data['bind_uid']);
            return $user['avatar'];
        }else{
            return '';
        }
    }

    public function getTypeTextAttr($value,$data)
    {
        $agency_member=new AgencyMember();
        return $agency_member->getTypeList($data['type']);
    }

}