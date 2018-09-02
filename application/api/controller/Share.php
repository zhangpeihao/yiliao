<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/16
 * Time: 16:24
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\TopicPostAdmire;
use EasyWeChat\Factory;
use fast\Random;

/**
 * 分享
 * Class Share
 * @package app\api\controller
 */
class Share extends Api{

    protected $noNeedRight='*';
    protected $noNeedLogin='*';

    private $option;

    private $app;

    public function __construct()
    {
        error_reporting(0);
        parent::__construct();
    }

    public function practice_page()
    {
        $data=model('Practice')->where('id',input('id'))->find();
        $comment=model('PracticeComment')->where('pid',input('id'))->order('id desc')->paginate(20,[],['page'=>1])->jsonSerialize();
//        dump($comment);
//dump($data->toArray());
//exit();
        db('practice')->where('id',input('id'))->setInc('read_count');
        return view('',['data'=>$data,'comment'=>$comment,'pid'=>input('id')]);
    }

    public function get_userinfo()
    {
        $option=[
            'app_id'=>'wx75311021ec636895',
            'secret'=>'959c5576375de41175ab5b4f27127e38',
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => '/api/share/oauth_callback',
            ],
        ];
        $app = Factory::officialAccount($option);
        $oauth = $app->oauth;
        // 未登录
        if (empty(session('wechat_user'))) {
//            return $oauth->redirect();
            // 这里不一定是return，如果你的框架action不是返回内容的话你就得使用
             $oauth->redirect()->send();
        }
    }

    public function oauth_callback()
    {
        // 获取 OAuth 授权结果用户信息
        $option=[
            'app_id'=>'wx75311021ec636895',
            'secret'=>'959c5576375de41175ab5b4f27127e38',
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => '/oauth_callback',
            ],
        ];
        $app = Factory::officialAccount($option);
        $oauth = $app->oauth;
        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();
        session('wechat_user',$user->toArray());
        $targetUrl = empty(session('target_url')) ? '/' : session('target_url');
        header('location:'. $targetUrl); // 跳转到 user/profile
    }

    public function topic_page()
    {
        if (is_weixin()) {
            session('target_url', request()->url());
            $this->get_userinfo();
        }
        if (session('wechat_user')) {
            //进行注册、登录
            $data = session('wechat_user');
            $open_id = $data['id'];
            $avatar = $data['avatar'];
            $nick_name = $data['nickname'];
            $unionid = $data['original']['unionid'];
            $platform = 'weixin';
            $user = \app\common\model\ThirdUser::get(['open_id' => $open_id]);
            if ($user) {
                //如果已经有账号则直接登录
                //如果已经有账号则直接登录
                $ret = $this->auth->direct($user->user_id);
                $new_member = 0;
            } else {
                $rand = genSecret(3);
                $nick_name = unicode2utf8($nick_name);
                if ($platform == 'weixin') {
                    $tmpNick = 'wx_' . $nick_name . $rand;
                } elseif ($platform == 'qq') {
                    $tmpNick = $platform . '_' . $nick_name . $rand;
                }
                $userData = array(
                    'avatar' => $avatar,
                );
                $ret = $this->auth->register($tmpNick, Random::alnum(), '', '', $userData);
                $new_member = 1;
                if ($ret) {
                    $thirdData = array(
                        'user_id' => $this->auth->id,
                        'avatar' => $avatar,
                        'nick_name' => $nick_name,
                        'open_id' => $open_id,
                        'platform' => $platform,
                        'unionid' => $unionid
                    );
                    \app\common\model\ThirdUser::create($thirdData);
                }
            }
        }
        $token = '';
        $uid=$this->auth->id;
        if (empty($uid)) {
            $uid= 0;
        } else {
            $token = $this->auth->getToken();
        }
        $data = model('TopicPost')->where('id', input('id'))->find();
        $data['is_admire'] = TopicPostAdmire::is_admire(input('id'), 0, $uid);
        db('topic_post')->where('id', input('id'))->setInc('readcount');
        $comment = model('TopicPostComment')->where(['pid' => input('id')])->order('id desc')->select();
        return view('topic_page2', ['data' => $data, 'comment' => $comment, 'pid' => input('id'), 'token' => $token]);
    }


    public function discuz_page()
    {
        if (is_weixin()) {
            session('target_url', request()->url());
            $this->get_userinfo();
        }
        if (session('wechat_user')) {
            //进行注册、登录
            $data = session('wechat_user');
            $open_id = $data['id'];
            $avatar = $data['avatar'];
            $nick_name = $data['nickname'];
            $unionid = $data['original']['unionid'];
            $platform = 'weixin';
            $user = \app\common\model\ThirdUser::get(['open_id' => $open_id]);
            if ($user) {
                //如果已经有账号则直接登录
                //如果已经有账号则直接登录
                $ret = $this->auth->direct($user->user_id);
                $new_member = 0;
            } else {
                $rand = genSecret(3);
                $nick_name = unicode2utf8($nick_name);
                if ($platform == 'weixin') {
                    $tmpNick = 'wx_' . $nick_name . $rand;
                } elseif ($platform == 'qq') {
                    $tmpNick = $platform . '_' . $nick_name . $rand;
                }
                $userData = array(
                    'avatar' => $avatar,
                );
                $ret = $this->auth->register($tmpNick, Random::alnum(), '', '', $userData);
                $new_member = 1;
                if ($ret) {
                    $thirdData = array(
                        'user_id' => $this->auth->id,
                        'avatar' => $avatar,
                        'nick_name' => $nick_name,
                        'open_id' => $open_id,
                        'platform' => $platform,
                        'unionid' => $unionid
                    );
                    \app\common\model\ThirdUser::create($thirdData);
                }
            }
        }
        $token = '';
        $uid=$this->auth->id;
        if (empty($uid)) {
            $uid= 0;
        } else {
            $token = $this->auth->getToken();
        }
        $data = model('DisPost')->where('id', input('id'))->find();
        $data['is_admire'] = TopicPostAdmire::is_admire(input('id'), 0, $uid);
        db('dis_post')->where('id', input('id'))->setInc('readcount');
        $comment = model('DisPostComment')->where(['pid' => input('id')])->order('id desc')->select();
        return view('topic_page2', ['data' => $data, 'comment' => $comment, 'pid' => input('id'), 'token' => $token]);
    }

        /**
         * 判断是IOS还是android
         * @author JiangZhang
         * @create 2016-04-07 22:07:00
         * @return string
         */
        public function down()
        {
            $clientkeywords = array('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile',
                'windvane',
                'oppo',
                'vivo',
                'honor',
                'hexcnfn',
                'huawei'
            );
            //获取客户端设备的类型
            $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            if (strpos($agent, 'micromessenger') && strpos($agent, 'iphone')) {
                //echo "<script>location.href='http://mp.weixin.qq.com/mp/redirect?url=https%3a%2f%2fitunes.apple.com%2fus%2fapp%2fhua-gong707%2fid1071173632%3fmt%3d8';</script>";die;
                //header("location:http://mp.weixin.qq.com/mp/redirect?url=https%3a%2f%2fitunes.apple.com%2fus%2fapp%2fhua-gong707%2fid1071173632%3fmt%3d8");
                //header("location:http://mp.weixin.qq.com/mp/redirect?url=https://itunes.apple.com/cn/app/hua-gong707/id1071173632?l=en&mt=8");
                //echo $html;
                return view();
            } elseif (strpos($agent, 'micromessenger')) {
                return view();
            } elseif (strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
                //	echo "<script>location.href='https://itunes.apple.com/cn/app/hua-gong707/id1071173632?l=en&mt=8';</script>";die;
//            header("Location:https://itunes.apple.com/app/id1289514031");exit;
                echo '<script>alert("IOS版应用还未上线，请耐心等待");window.history.go(-1);</script>';
                exit();
                //}elseif (strpos($agent, 'android')) {
            } elseif (preg_match("/(" . implode('|', $clientkeywords) . ")/i", $agent)) {
//                $clientInfo = model('Client')->getClient(array('platform' => 'android', 'status' => 1));
                $clientInfo=model('version')->where(array('target'=>3,'platform'=>'android','status'=>'normal'))->order('newversion desc')->find();
                if (empty($clientInfo)){
                    echo '<script>alert("安卓版应用还未上线，请耐心等待");window.history.go(-1);</script>';
                    exit();
                }
                $url = $clientInfo['downloadurl'];
                //	header("Location:http://app.hg707.com/hg707v5.5.3.apk");
                header("Location:" . $url);
                exit;
            } else {
//                $clientInfo = model('Client')->getClient(array('platform' => 'android', 'status' => 1));
//                $url = $clientInfo['url'];
                $clientInfo=model('version')->where(array('target'=>3,'platform'=>'android','status'=>'normal'))->order('newversion desc')->find();
                if (empty($clientInfo)){
                    echo '<script>alert("安卓版应用还未上线，请耐心等待");window.history.go(-1);</script>';
                    exit();
                }
                $url = $clientInfo['downloadurl'];
                //	header("Location:http://app.hg707.com/hg707v5.5.3.apk");
                header("Location:" . $url);
                exit;
                //$this->display();
            }
        }

}