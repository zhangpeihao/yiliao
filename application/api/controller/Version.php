<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/27 16:01
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 版本更新
 * Class Version
 * @package app\api\controller
 */
class Version extends Api{

    protected $noNeedLogin='*';

    protected $noNeedRight='*';
    /**
     * 获取做新版本号
     * @ApiMethod   (POST)
     * @ApiParams   (name="target", type="int", required=true, description="所属终端：1教务端，2家长端，3：C端")
     * @ApiParams   (name="platform", type="string", required=true, description="平台：ios/android")
     * @ApiParams   (name="appversion", type="string", required=true, description="当前版本号：如：1.0.1")
     * @ApiReturnParams   (name="platform", type="string", required=true, description="平台：ios/android")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/27 16:39
     */
    public  function getVersion(){
        $params=$this->request->param('');
        $target=trim($params['target']);
        $platform=trim($params['platform']);
        $version=strval($params['appversion']);
        if (empty($target)){$this->error('请指定终端');}
        if (empty($platform)){$this->error('请指定平台');}
        if (empty($version)){$this->error('请指定当前版本号');}
        if ('ios'!=$platform && 'android'!=$platform){
            $platform='ios';
        }
        $clientInfo=model('version')->where(array('target'=>$target,'platform'=>$platform,'status'=>'normal'))->order('newversion desc')->find();
        if ($clientInfo){
            $clientInfo['summary']=explode("\r\n",$clientInfo['content']);
            if($platform=='ios'){
                if (version_compare($version,$clientInfo['newversion'],'<=')){
                    $this->success('获取版本号成功',$clientInfo);
                }
            }
            $this->success('获取版本号成功',$clientInfo);
        }
        $this->error('暂无新版本');
    }
}