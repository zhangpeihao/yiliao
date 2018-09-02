<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2017/12/4 19:04
 */
namespace app\api\controller\backend;

use app\common\controller\Api;
use app\common\model\Camera;
use app\common\model\CameraPower;

/**
 * 教务端视频监控
 * Class Monitor
 * @package app\api\controller\backend
 */
class Monitor extends Api {

    protected $noNeedLogin='*';
    protected $noNeedRight='*';

    private $appkey='399e71b670bf4dc5892b777ff808546d';
    private $secret='93a076a557cfd22efb5974f640e3498f';

    /**
     * 获取萤石云平台的AccessToken
     * @ApiMethod   (GET)
     * @ApiParams   (name="", type="string", required=true, description="")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/24 15:44
     */
    public function getAccessToken(){
        if (!session('accessToken')){
            $this->req_token();
        }
        $data=session('accessToken');
        $data['appkey']=$this->appkey;
        $this->success('获取成功',$data);
    }


    private function req_token(){
        if (!session('accessToken')){
            $token=curl_data('https://open.ys7.com/api/lapp/token/get',
                ['appKey'=>$this->appkey,'appSecret'=>$this->secret],
                'POST'
            );
            $token=json_decode($token,true);
            session('accessToken',$token['data']);
        }
        $accessToken=session('accessToken.accessToken');
        if (empty($accessToken)){
            $this->req_token();
        }
        return session('accessToken.accessToken');
    }


    /**
     * 获取设备列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="", type="", required=true, description="")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/24 15:45
     */
    private function getDeviceList(){
        $page=request()->get('page',1,'intval');
        $token=$this->req_token();
        $device=curl_data(
//            'https://open.ys7.com/api/lapp/device/list',
            'https://open.ys7.com/api/lapp/camera/list',
            ['accessToken'=>$token,'pageStart'=>0,'pageSize'=>50],
            'POST'
        );
        $device=json_decode($device,true);
        foreach ($device['data'] as &$val){

            $check=db('camera')->where(['deviceSerial'=>$val['deviceSerial']])->find();
            if (!$check){
                Camera::create($val,true);
                $check['id']=model('Camera')->getLastInsID();
            }else{
                Camera::update($val,['id'=>$check['id']],true);
            }
            $val['id']=$check['id'];
        }
        $this->success('获取成功',$device['data']);

    }

    /**
     * 获取我的监控设备列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="", type="string", required=true, description="")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/24 17:16
     */
    public function get_my_device()
    {
        $uid=$this->auth->id;
        $agency_id=$this->auth->agency_id;
        $deviceSerial=db('camera_power')->where(['uid'=>$uid,'power_type'=>1])
                        ->whereOr(['agency_id'=>$agency_id,'power_type'=>2])
                        ->distinct(true)
                        ->column('deviceSerial');
        $data=model('camera')->where('deviceSerial','in',$deviceSerial)
                ->field('id,creator,status,deviceSerial,deviceName,channelNo,channelName,address,ctime')
                ->select();
        $this->success('查询成功',$data);
    }
}