<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/16
 * Time: 10:29
 */
namespace app\api\controller;

use app\common\controller\Api;
use fast\Random;

/**
 * 第三方登录
 * Class ThirdUser
 * @package app\api\controller
 */
class ThirdUser extends Api{
    protected $noNeedLogin='*';
    protected $noNeedRight='*';

    protected $_user = NULL;
    protected $_token = '';
    protected $keeptime = 2592000;
    /**
     * 用户的第三方登录
     * @ApiMethod   (POST)
     * @ApiParams   (name="open_id", type="string", required=true, description="第三方登录open_id")
     * @ApiParams   (name="nick_name", type="string", required=true, description="昵称")
     * @ApiParams   (name="avatar", type="string", required=true, description="头像")
     * @ApiParams   (name="platform", type="string", required=true, description="第三方平台：qq、weixin")
     * @ApiParams   (name="unionId", type="string", required=true, description="微信开放平台统一id")
     * @ApiReturnParams   (name="userinfo", type="array", required=true, description="用户信息，array，同手机号登录接口")
     * @ApiReturnParams   (name="new_member", type="int", required=true, description="用是否为新用户：0否 1是")
     * @ApiReturn (data="{'code':1,'msg':'Logged in successful','time':'1526445028','data':{'userinfo':{'id':15,'agency_id':0,'username':'qq_23123123110110','nickname':'qq_23123123110110','mobile':'','avatar':'https:\/\/music.588net.com1231231','score':0,'agency':[],'join_agency_list':[],'token':'234c4d6a-9155-4b97-8026-0ef3fbc3c9d7','group':{'id':2,'name':'普通会员','rules':'','createtime':1522656815,'updatetime':1522656836,'status':'normal','rules_list':[]}},'new_member':0}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/16 10:38
     */
    public function thirdLogin(){
        $open_id=$this->request->post('open_id','','strval');
        $nick_name=$this->request->post('nick_name','','strval');
        $avatar=$this->request->post('avatar','','strval');
        $platform=$this->request->post('platform','','strval');
        $unionid=$this->request->post('unionId','','strval');
        $nick_name=unicode2utf8($nick_name);
        $rand=genSecret(3);
        if (empty($open_id)){$this->error('openid不能为空');}
        if (empty($nick_name)){$this->error('昵称不能为空');}
        if (empty($avatar)){
//            $this->error('头像不能为空');
            $avatar=config('img_domain').'/assets/img/avatar.png';
        }
        if (empty($platform)){$this->error('平台不能为空');}
        if (empty($unionid) && $platform=='weixin'){$this->error('应用标识不能为空');}
        if ($platform =='weixin'){
            $nick_name=$nick_name.$rand;
        }elseif ($platform =='qq'){
            $nick_name=$nick_name.$rand;
        }
        $user = \app\common\model\ThirdUser::get(['open_id'=>$open_id]);
        if ($user)
        {
            //如果已经有账号则直接登录
            $ret = $this->auth->direct($user->user_id);
            $new_member=0;
        }
        else
        {
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
                    'unionid'=>$unionid
                );
                \app\common\model\ThirdUser::create($thirdData);
            }
        }
        if ($ret)
        {
            $data = ['userinfo' => $this->auth->getUserinfo(),'new_member'=>$new_member];
            $this->success(__('Logged in successful'), $data);
        }
        else
        {
            $this->error($this->auth->getError());
        }

    }


}