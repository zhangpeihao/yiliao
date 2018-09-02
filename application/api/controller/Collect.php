<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/22
 * Time: 13:50
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 客户端统计
 * Class Collect
 * @package app\api\controller
 */
class Collect extends Api
{
    protected $noNeedRight='*';

    protected $noNeedLogin='*';

    /**
     * 手机设备信息
     * @ApiMethod   (POST)
     * @ApiParams   (name="imei", type="string", required=true, description="imei")
     * @ApiParams   (name="system", type="string", required=true, description="系统")
     * @ApiParams   (name="system_version", type="string", required=true, description="系统版本号")
     * @ApiParams   (name="mac_model", type="string", required=true, description="机型号")
     * @ApiParams   (name="network", type="string", required=true, description="网络类型")
     * @ApiParams   (name="version", type="string", required=true, description="APP版本号")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/22 13:52
     */
    public function collect_data()
    {
        $param=$this->request->post();
        $mac_code=strval($param['imei']);
        $system=strval($param['system']);
        $system_version=strval($param['system_version']);
        $mac_model=strval($param['mac_model']);
        $ip=request()->ip(0);
        $uid=$this->auth->id;
        $time=time();
        $date=date("Y-m-d",$time);
        $network=strval($param['network']);
        $version=strval($param['version']);
        $res=GetIpLookup($ip);
//        $loc=$res['country'].$res['province'].$res['city'].$res['district'];
        $loc=$res['address'];
        $data=[
            'uid'=>$uid,
            'username'=>$this->auth->username,
            'mac'=>$mac_code,
            'system'=>$system,
            'system_version'=>$system_version,
            'mac_model'=>$mac_model,
            'ip'=>$ip,
            'date'=>$date,
            'ctime'=>$time,
            'network'=>$network,
            'version'=>$version,
            'location'=>$loc
        ];
        $info=db('user_collect')->insertGetId($data);
        $this->success('上传成功',$data);
    }
}