<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * Token接口
 */
class Token extends Api
{

    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 检测Token是否过期
     * @ApiMethod   (POST)
     * @ApiParams   (name="", type="", required=true, description="")
     * @ApiReturnParams   (name="token", type="string", required=true, description="当前的token")
     * @ApiReturnParams   (name="expires_in", type="timestamp", required=true, description="剩余有效秒数")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1526636764','data':{'token':'f9c5a5ca-998a-4f09-820c-c84928cfe1bb','expires_in':2513968}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/7 11:38
     */
    public function check()
    {
        $token = $this->auth->getToken();
        $tokenInfo = \app\common\library\Token::get($token);
        $this->success('查询成功', ['token' => $tokenInfo['token'], 'expires_in' => $tokenInfo['expires_in']]);
    }

    /**
     * 刷新Token
     * @ApiMethod   (POST)
     * @ApiParams   (name="", type="", required=true, description="")
     * @ApiReturnParams   (name="token", type="string", required=true, description="刷新后的token")
     * @ApiReturnParams   (name="expires_in", type="timestamp", required=true, description="有效期")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/7 11:39
     */
    public function refresh()
    {
        $token = $this->auth->getToken();
        $tokenInfo = \app\common\library\Token::get($token);
        $tokenInfo['expiretime'] = time() + 2592000;
        \app\common\model\Token::update($tokenInfo,['token'=>$tokenInfo['token']],true);
        $this->success('获取成功', ['token' => $tokenInfo['token'], 'expires_in' => $tokenInfo['expiretime']-time()]);
    }

}
