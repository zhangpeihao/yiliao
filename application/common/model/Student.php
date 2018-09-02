<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/3 11:30
 */
namespace app\common\model;

use app\common\library\Auth;
use think\Model;

class Student extends Model{

    protected $name='student';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'gender_text',
        'learn_status_text',
        'status_text',
        'student_id',
        'student',
        'level_info'
    ];

    protected static function init()
    {
        Student::event('before_insert',function ($student){
           $auth=Auth::instance();
           $student->agency_id=$auth->agency_id;
        });
    }

    public function getLevelInfoAttr($value,$data)
    {
        $default=\model('UserLevel')->field('id,level,levelname,discount')->where(['level'=>0,'agency_id'=>$data['agency_id']])->find();
        if (!empty($data['level'])){
            $res=\model('UserLevel')->field('id,level,levelname,discount')->where(['id'=>$data['level']])->find();
            if (!empty($res)){
                return $res->toArray();
            }
        }else{
            return $default;
        }
    }

    public function getRestLessonAttr($value,$data)
    {
        if ($value<0){
            return 0;
        }else{
            return $value;
        }
    }

    public function getStudentIdAttr($value,$data)
    {
        return $data['id'];
    }

    public function getStudentAttr($value,$data)
    {
        return $data['username'];
    }

    public function getGenderList()
    {
        return ['1' =>'男','2'=>'女','0'=>'未知'];
    }

    public function getLearnStatusList()
    {
        return ['1' =>'在读','2'=>'试听',3=>'过期',0=>'未知'];
    }

    public function getStatusList()
    {
        return ['0'=>'禁用','1' => '未签约',2=>'未排课'];
    }



    public function getGenderTextAttr($value, $data)
    {
        if (empty($data['gender'])){$data['gender']=0;}
        $value = $value ? $value : $data['gender'];
        $list = $this->getGenderList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getLearnStatusTextAttr($value, $data)
    {
        if (empty($data['learn_status'])){$data['learn_status']=0;}
        $value = $value ? $value : $data['learn_status'];
        $list = $this->getLearnStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        if (empty($data['status'])){$data['status']=0;}
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getCreatorAttr($value,$data)
    {
        if (empty($value)){return "";}
        return db('User')->where('id',$value)->value('username');
    }

    /**
     * 获取头像
     * @param   string    $value
     * @param   array     $data
     * @return string
     */
    public function getAvatarAttr($value)
    {
        if(strstr($value,'http')){
            return $value;
        }
        return config('img_domain').($value ? $value : '/assets/img/avatar.png');
    }


    public function getByMobile($mobile)
    {
        return $this->where('mobile',$mobile)->find();
    }

    public function getById($id)
    {
        $userinfo=[];
        $data=$this->find($id);
        if ($data){
            $userinfo=$data->data;
            $userinfo['avatar']=$this->getAvatarAttr($userinfo['avatar']);
        }
        return $userinfo;
    }

}