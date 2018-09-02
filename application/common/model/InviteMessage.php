<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 10:58
 */
namespace app\common\model;

use fast\Random;
use think\Model;
use app\common\library\Sms as Smslib;

class InviteMessage extends Model{
    protected $name='invite_message';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    public function create_msg($user,$to_uid)
    {
        $teacher=Teacher::get($to_uid);
        $code=Random::uuid();
        $title=$user->username.'邀请您加入远航音乐工作室';
        $content='一起来用远航音乐教务APP，提升教务管理效率，赶紧加入吧。';
        $url=config('api_url').url('api/invite/index',['code'=>$code]);
        $data=[
            'username'=>$user->username,
            'to_username'=>$teacher['username'],
            'to_mobile'=>$teacher['mobile'],
            'title'=>$title,
            'content'=>$content,
            'code'=>$code,
            'url'=>$url,
            'status'=>0,
            'creator'=>$user->id
        ];
        $ret=$this->data($data);
//        $ret = Smslib::notice($teacher['mobile'], [$user['username'],$url], '242040');
        if ($ret)
        {
            $this->update(['status'=>1],['id'=>$this->getLastInsID()]);
           return $data;
        }
        else
        {
            return false;
        }
    }
}