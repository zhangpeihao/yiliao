<?php

namespace app\common\model;

use think\Model;

/**
 * 会员模型
 */
class User Extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'url','score_rmb','level_info'
    ];

    public function getLevelInfoAttr($value,$data)
    {
        if (empty($data['level'])){$data['level']=0;}
        $res=\model('UserLevel')->find($data['level']);
        if (empty($res)){
            return [];
        }else{
            return $res->toArray();
        }
    }

    protected static function init()
    {
        User::event('after_insert',function ($user){
           if ($user['mobile']){
               $check=Teacher::get(['mobile'=>$user['mobile'],'is_bind'=>0]);
               if ($check){
                   $info=Teacher::update(['is_bind'=>1,'bind_uid'=>$user['id']],['id'=>$check['id']]);
                   if ($info){
                       User::update(['group_id'=>4],['id'=>$user['id']]);
                   }
               }
           }
        });
    }

    /**
     * 转换金币兑换人民币，整十取值，不足
     * @param string $value
     * @param array $data
     * @return string
     */
    public function getScoreRmbAttr($value,$data=[])
    {
        $rate=config('site.rmb_score',10);
        if (empty($data['score'])){$data['score']=0;}
        $int_num=floor($data['score']/($rate*10));
        $can_pay=$int_num*10;
        if ($can_pay<100){
            return 0;
        }
        return $can_pay;
    }

    /**
     * 获取个人URL
     * @param   string  $value
     * @param   array   $data
     * @return string
     */
    public function getUrlAttr($value, $data)
    {
        return "/u/" . $data['id'];
    }

    public function getBirthdayAttr($value,$data)
    {
        return strval($value);
    }

    /**
     * 获取头像
     * @param   string    $value
     * @param   array     $data
     * @return string
     */
    public function getAvatarAttr($value, $data)
    {
        if (!strstr($value,'http')){
            return config('img_domain').($value ? $value : '/assets/img/avatar.png');
        }else{
            return $value;
        }
    }

    /**
     * 获取会员的组别
     */
    public function getGroupAttr($value, $data)
    {
        $group=UserGroup::get($data['group_id']);
        $group['rules_list']=UserRule::get_user_rule([],$data['group_id']);
        return $group;
    }

    /**
     * 获取验证字段数组值
     * @param   string    $value
     * @param   array     $data
     * @return  object
     */
    public function getVerificationAttr($value, $data)
    {
        $value = array_filter((array) json_decode($value, TRUE));
        $value = array_merge(['email' => 0, 'mobile' => 0], $value);
        return (object) $value;
    }

    /**
     * 设置验证字段
     * @param mixed $value
     * @return string
     */
    public function setVerificationAttr($value)
    {
        $value = is_object($value) || is_array($value) ? json_encode($value) : $value;
        return $value;
    }

    /**
     * 变更会员积分
     * @param int $score    积分
     * @param int $user_id  会员ID
     * @param string $memo  备注
     */
    public static function score($score, $user_id, $memo)
    {
        $user = self::get($user_id);
        if ($user)
        {
            $before = $user->score;
            $after = $user->score + $score;
            $level = self::nextlevel($after);
            //更新会员信息
            $user->save(['score' => $after, 'level' => $level]);
            //写入日志
            ScoreLog::create(['user_id' => $user_id, 'score' => $score, 'before' => $before, 'after' => $after, 'memo' => $memo]);
        }
    }

    /**
     * 根据积分获取等级
     * @param int $score 积分
     * @return int
     */
    public static function nextlevel($score = 0)
    {
        $lv = array(1 => 0, 2 => 30, 3 => 100, 4 => 500, 5 => 1000, 6 => 2000, 7 => 3000, 8 => 5000, 9 => 8000, 10 => 10000);
        $level = 1;
        foreach ($lv as $key => $value)
        {
            if ($score >= $value)
            {
                $level = $key;
            }
        }
        return $level;
    }

    public function getByMobile($mobile)
    {
        return $this->where('mobile',$mobile)->find();
    }
    public function getByUsername($username)
    {
        return $this->where('username',$username)->find();
    }
}
