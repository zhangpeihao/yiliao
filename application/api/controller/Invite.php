<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 10:47
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\InviteMessage;
use fast\Random;

/**
 * 邀请H5页面
 * Class Invite
 * @package app\api\controller
 */
class Invite extends Api{
    protected $noNeedLogin='*';
    
    /**
     * 邀请页链接地址
     * @ApiMethod  (GET)
     * @ApiRoute  {/api/invite/index}
     * @ApiParams   (name="code", type="string", required=true, description="校验码")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/9 17:00
     */
    public function index()
    {
        $code=$this->request->request('code');
        $data=InviteMessage::get(['code'=>$code]);
        return view('',['data'=>$data]);
    }

    public function user_share_index()
    {
        $share_url=config('img_domain').url('api/invite/join_app',['code'=>input('code')]);
        return view('user_share_index',['share_code'=>input('code'),'share_url'=>$share_url]);
    }

    /**
     * 远航音乐邀请页
     * @ApiMethod   (GET)
     * @ApiParams   (name="", type="", required=true, description="")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/14 19:52
     */
    public function join_app()
    {
        return view('',['from_code'=>input('from_code')]);
    }

    /**
     * 手机号验证注册
     * @ApiMethod   (POST)
     * @ApiParams   (name="mobile", type="string", required=true, description="手机号")
     * @ApiParams   (name="code", type="string", required=true, description="验证码，通过94：发送验证码： /api/sms/send接口获取，参数event：register")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/14 19:53
     */
    public function reg_confirm()
    {
        $mobile=request()->post('mobile','','strval');
        $code=request()->post('code','','strval');
        $from_code=request()->post('from_code','','strval');
        if (!\think\Validate::regex($mobile, "^1\d{10}$"))
        {
            $this->error(__('Mobile is incorrect'));
        }
        if (!\app\common\library\Sms::check($mobile, $code, 'register'))
        {
            $this->error(__('Captcha is incorrect'));
        }
        \app\common\library\Sms::flush($mobile, 'register');

        $check=\app\common\model\User::get(['mobile'=>$mobile,'status'=>1]);
        if ($check)
        {
            $ret = $this->auth->register(substr_replace($mobile,'****',3,4), Random::alnum(), '', $mobile, []);
            if ($ret){
                /*******************活动逻辑部分**************************/
                $from_uid=base64_decode($from_code);
                //1=>'发布练习视频',2=>'商城消费兑换',3=>'商城消费',4=>'拉新赠送'，5=>'新用户注册'
                $score_data=[
                    'uid'=>$from_uid,
                    'num'=>config('site.from_user_score'),
                    'operate'=>1,
                    'type'=>4,
                    'creator'=>'system',
                    'link_id'=>$this->auth->id,
                    'remark'=>'邀请新用户赠送'
                ];
                $res_info=\app\common\model\UserScore::changeScore($score_data);
                if ($res_info){
                    $score_data=[
                        'uid'=>$this->auth->id,
                        'num'=>config('site.new_user_score'),
                        'operate'=>1,
                        'type'=>5,
                        'creator'=>'system',
                        'link_id'=>$from_uid,
                        'remark'=>'新用户注册赠送'
                    ];
                    $res_info=\app\common\model\UserScore::changeScore($score_data);
                }
                $this->success('恭喜您加入成功',['userinfo' => $this->auth->getUserinfo()]);
            }else{
                $this->error('很遗憾，参与失败');
            }
        }
        else
        {
            $this->error("很遗憾您已经是老客户，不符合参与条件");
        }
    }
}