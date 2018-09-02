<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/24
 * Time: 19:32
 */
namespace app\common\model;

use think\Db;
use think\Model;

class UserScore extends Model{
    protected $name='user_score';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    public function getCtimeAttr($value,$data)
    {
        return formatTime($value);
    }
    public function getUtimeAttr($value,$data)
    {
        return formatTime($value);
    }

    public function getTypeList()
    {
        return [1=>'发布练习视频',2=>'商城消费兑换',3=>'商城消费',4=>'拉新赠送',5=>'新用户注册'];
    }

    public function getOperateList()
    {
        return [-1=>'减',1=>'加'];
    }
    
    public static function changeScore($data=[]){
        $data['uid']=intval($data['uid']);
        $data['num']=abs(floatval($data['num']));
        $data['last_num']=db('User')->where(array('id'=>$data['uid']))->value('score');
        $data['operate']=intval($data['operate']);
        $data['link_order']=!empty($data['link_order'])?$data['link_order']:"";
        $data['type']=strval($data['type']);
        $data['creator']=strval($data['creator']);
        $data['remark']=strval($data['remark']);
        $data['ctime']=time();
        $data['utime']=time();
        Db::startTrans();
        $info=Db::name('UserScore')->insertGetId($data);
        if ($info){
            try{
                if (empty($data['num'])){
                    $res=true;
                }else{
                    if ($data['operate']=='-1'){
                        $res=db('User')->where(['id'=>$data['uid']])->setDec('score',$data['num']);
                    }else{
                        $res=db('User')->where(['id'=>$data['uid']])->setInc('score',$data['num']);
                    }
                }
            }catch (\Exception $e){
                Db::rollback();
                return false;
            }
        }
        Db::commit();
        if ($res){
            return true;
        }else{
            return false;
        }
    }

}