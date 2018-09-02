<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/21
 * Time: 15:21
 */
namespace app\api\controller;


use app\common\controller\Api;
use EasyWeChat\Factory;
use fast\Random;

/**
 * 微信小程序
 * Class Wechat
 * @package app\api\controller
 */
class Wechat extends Api{
    protected $noNeedLogin=['setinfo'];

    protected $noNeedRight='*';

    private $config;

    public function __construct()
    {
        parent::__construct();
        $this->config=config('wechat_mini');
    }


    /**
     * 微信小程序登录
     * @ApiMethod   (POST)
     * @ApiParams   (name="code", type="string", required=true, description="小程序login获取的code")
     * @ApiParams   (name="iv", type="string", required=true, description="识别码")
     * @ApiParams   (name="encryptedData", type="string", required=true, description="加密数据")
     * @ApiReturnParams   (name="userinfo", type="array", required=true, description="用户信息")
     * @ApiReturnParams   (name="new_member", type="int", required=true, description="是否为新用户：0否，1是")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/21 15:37
     */
    public function setinfo()
    {
        $iv=request()->post('iv');
        $encryptData=request()->post('encryptedData');
        $code=request()->post('code');
        if (empty($iv) || empty($encryptData) || empty($code)){
            $this->error('参数错误');
        }
        $app = Factory::miniProgram($this->config);
        //获取session
        $check_sesson=$app->auth->session($code);
        $session=$check_sesson['session_key'];
        //解析文本
        $data=$app->encryptor->decryptData($session,$iv,$encryptData);
        //进行注册、登录
        $open_id=$data['openId'];
        $avatar=$data['avatarUrl'];
        $nick_name=$data['nickName'];
        $platform='weixin';
        $user = \app\common\model\ThirdUser::get(['open_id'=>$open_id]);
        if ($user)
        {
            //如果已经有账号则直接登录
            //如果已经有账号则直接登录
            $ret = $this->auth->direct($user->user_id);
            $new_member=0;
        }else{
            $rand=genSecret(3);
            $nick_name=unicode2utf8($nick_name);
            if ($platform=='weixin'){
                $tmpNick='wx_'.$nick_name.$rand;
            }elseif ($platform=='qq'){
                $tmpNick=$platform.'_'.$nick_name.$rand;
            }
            $userData=array(
                'avatar' =>$avatar,
            );
            $ret = $this->auth->register($tmpNick, Random::alnum(), '', '', $userData);
            $new_member=1;
            if ($ret){
                $thirdData=array(
                    'user_id' =>$this->auth->id,
                    'avatar'=>$avatar,
                    'nick_name'=>$nick_name,
                    'open_id' =>$open_id,
                    'platform'=>$platform,
//                    'unionid'=>$unionid
                );
                \app\common\model\ThirdUser::create($thirdData);
            }
        }
        if ($ret)
        {
            $data = ['userinfo' => $this->auth->getUserinfo(),'new_member'=>$new_member];
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }


    /**
     * 绑定手机号
     * @ApiMethod   (POST)
     * @ApiParams   (name="mobile", type="string", required=true, description="手机号")
     * @ApiParams   (name="code", type="string", required=true, description="验证码，通过验证码接口，event：bind_mobile")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/21 15:55
     */
    public function bind_mobile()
    {
        $mobile=$this->request->post('mobile','','strval');
        $code=$this->request->post('code','','strval');
        $rule=['mobile'=>'require','openid'=>'require','code'=>'require'];
        $msg=['mobile'=>'手机号','openid'=>'微信openid','code'=>'验证码'];
        $validate=new \think\Validate($rule,[],$msg);
        $data=['mobile'=>$mobile,'code'=>$code];
        $check=$validate->check($data);
        if (!$check){$this->error($validate->getError());}
        $mobile_check=\app\common\model\User::get(['mobile'=>$mobile,'status'=>1]);
        if ($mobile_check){
            $this->error('该手机号已绑定');
        }else{
            if (!\app\common\library\Sms::check($mobile, $code, 'bind_mobile'))
            {
                $this->error(__('Captcha is incorrect'));
            }
            $info=\app\common\model\User::update(['mobile'=>$mobile],['id'=>$this->auth->id]);
            if ($info){
                $this->success('绑定成功',['userinfo'=>$this->auth->getUserinfo()]);
            }else{
                $this->error('绑定失败');
            }
        }
    }
}