<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/16 11:13
 */
namespace app\api\controller;


use app\common\controller\Api;
use think\Db;
use think\Exception;

/**
 * 用户消息服务
 * Class UserMessage
 * @package app\api\controller
 */
class UserMessage extends Api{

    /**
     * 添加极光推送的附加信息
     * @ApiMethod   (POST)
     * @ApiParams   (name="alias", type="int", required=true, description="设备别名")
     * @ApiParams   (name="registrationId", type="string", required=true, description="设备在极光注册的id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/16 11:27
     */
    public function addinfo(){
        $alias=$this->request->post('alias','','strval');
        $registrationId=$this->request->post('registrationId','','strval');
        $user_id=$this->auth->id;
        if ($user_id){
            $check=db('user')->where(array('id'=>$user_id))->find();
            if (empty($check)){$this->error('用户不存在');}
            if ($alias){$data['alias']=$alias;}
            if ($registrationId){
                $data['registrationId']=$registrationId;
                vendor('JPush.JPush');
                try{
                    $client=new \JPush('4a9780b14647d477a300a514', '698a3cbfa6cf450289dbadf7');
                    $res=$client->device()->updateDevice($data['registrationId'],"$user_id");
                    if ($res){$data['alias']=$user_id;}
                    $info=\app\common\model\User::update($data,array('id'=>$user_id),true);
                    if ($info){
                        $this->success('更新成功');
                    }else{
                        $this->error('更新失败');
                    }
                }catch (Exception $e){}
            }else{
                $this->error('操作失败');
            }

        }
    }

}