<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/18 15:01
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\AgencyMember;

/**
 * 机构管理
 * Class Agency
 * @package app\api\controller
 */
class Agency extends Api{
    protected $noNeedRight='*';

    /**
     * 创建机构
     * @ApiMethod   (POST)
     * @ApiParams   (name="logo", type="string", required=true, description="logo链接")
     * @ApiParams   (name="name", type="string", required=true, description="机构名称")
     * @ApiParams   (name="mobile", type="string", required=true, description="机构联系电话")
     * @ApiParams   (name="address", type="string", required=true, description="机构地址")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/18 15:10
     */
    public function add()
    {
        $logo=$this->request->post('logo');
        $name=$this->request->post('name');
        $mobile=$this->request->post('mobile');
        $address=$this->request->post('address');

        $rule=[
            'logo'=>'require','name'=>'require','mobile'=>'require','address'=>'require'
        ];
        $msg=[
            'logo'=>'机构LOGO','name'=>'机构名称','mobile'=>'机构联系电话','address'=>'地址'
        ];
        $data=[
            'logo'=>$logo,'name'=>$name,'mobile'=>$mobile,'address'=>$address
        ];
        $validate=new \think\Validate($rule,[],$msg);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            if (\app\common\model\Agency::get(['name'=>$name,'status'=>1])){
                $this->error('该机构已存在');
            }else{
                $data['creator']=$this->auth->id;
                $data['members']=1;
                $info=\app\common\model\Agency::create($data);
                if ($info){
                    $this->success('添加成功');
                }else{
                    $this->error('操作失败');
                }
            }
        }
    }

    /**
     * 编辑机构
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="机构id")
     * @ApiParams   (name="logo", type="string", required=true, description="logo链接")
     * @ApiParams   (name="name", type="string", required=true, description="机构名称")
     * @ApiParams   (name="mobile", type="string", required=true, description="机构联系电话")
     * @ApiParams   (name="address", type="string", required=true, description="机构地址")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/18 15:09
     */
    public function edit()
    {
        $id=$this->request->post('id');
        $logo=$this->request->post('logo');
        $name=$this->request->post('name');
        $mobile=$this->request->post('mobile');
        $address=$this->request->post('address');
        $rule=[
            'id'=>'require|gt:0','logo'=>'require','name'=>'require','mobile'=>'require','address'=>'require'
        ];
        $msg=[
            'id'=>'机构ID','logo'=>'机构LOGO','name'=>'机构名称','mobile'=>'机构联系电话','address'=>'地址'
        ];
        $data=[
            'id'=>$id,'logo'=>$logo,'name'=>$name,'mobile'=>$mobile,'address'=>$address
        ];
        $validate=new \think\Validate($rule,[],$msg);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            $data['updator']=$this->auth->id;
            $info=\app\common\model\Agency::update($data,['id'=>$id]);
            if ($info){
                $this->success('更新成功');
            }else{
                $this->error('操作失败');
            }
        }
    }

    /**
     * 设置要切换登录的机构
     * @ApiMethod   (POST)
     * @ApiParams   (name="agency_id", type="int", required=true, description="机构id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/18 15:40
     */
    public function set_join_agency()
    {
        $agency_id=$this->request->post('agency_id');
        $info=\app\common\model\User::update(['agency_id'=>$agency_id],['id'=>$this->auth->id]);
        if ($info){
            $teacher=\app\common\model\Teacher::get(['agency_id'=>$agency_id,'id'=>$this->auth->id]);
            if ($teacher){
                if ($teacher['status']==0){
                    $this->error('身份信息不存在');
                }
                AgencyMember::update(['uid'=>$this->auth->id],['agency_id'=>$agency_id,'teacher_id'=>$teacher['id']]);
            }
            $user=new User();
            $user->getUserInfo('切换机构成功');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 会员通过机构编号加入机构
     * @ApiMethod   (POST)
     * @ApiParams   (name="sno", type="string", required=true, description="机构编号")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/24 16:34
     */
    public function user_entry_agency()
    {
        $sno=request()->post('sno','','strval');
        if (empty($sno)){$this->error('请输入机构编号');}
        $agency_info=\app\common\model\Agency::get(['sno'=>$sno]);
        if (empty($agency_info)){$this->error('该机构不存在');}
        $check=AgencyMember::get(['agency_id'=>$agency_info['id'],'uid'=>$this->auth->id]);
        if (empty($check)){
            $info=AgencyMember::create([
                'type'=>4,'agency_id'=>$agency_info['id'],'uid'=>$this->auth->id,'status'=>1,
                'creator'=>$agency_info['creator']
            ],true);
        }elseif ($check['status']==1){
            $this->success('您已经加入过了');
        }elseif($check['status']==0){
            $info=AgencyMember::update(['status'=>1,'updator'=>$this->auth->id],['id'=>$check['id']]);
        }
        if ($info){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
}