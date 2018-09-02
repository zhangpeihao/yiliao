<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/24
 * Time: 17:25
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 收货地址
 * Class UserAddress
 * @package app\api\controller
 */
class UserAddress extends Api{

    protected $noNeedRight='*';


    /**
     * 获取用户收货地址列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="", type="", required=true, description="")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 17:39
     */
    public function get_list()
    {
        $map=[];
        $map['isdelete']=0;
        $map['uid']=$this->auth->id;
        $data=model("UserAddress")->where($map)->order('update_time desc')->select();
        $this->success('获取成功',$data);
    }

    /**
     * 获取本人默认收货地址
     * @ApiMethod   (GET)
     * @ApiParams   (name="", type="", required=true, description="")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/29 12:16
     */
    public function get_my_default()
    {
        $data=model('UserAddress')->where(['uid'=>$this->auth->id,'isdefault'=>1])->find();
        if (empty($data)){
            $data=(object)[];
        }
        $this->success('查询成功',$data);
    }

    /**
     * 添加收货地址
     * @ApiMethod   (POST)
     * @ApiParams   (name="username", type="string", required=true, description="收货人姓名")
     * @ApiParams   (name="mobile", type="string", required=true, description="手机号")
     * @ApiParams   (name="province", type="string", required=false, description="省、自治区")
     * @ApiParams   (name="city", type="string", required=false, description="城市")
     * @ApiParams   (name="district", type="string", required=false, description="区县")
     * @ApiParams   (name="address", type="string", required=true, description="详细地址")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 17:34
     */
    public function add_address()
    {
        $username=request()->post('username');
        $mobile=request()->post('mobile');
        $province=request()->post('province','','strval');
        $city=request()->post('city','','strval');
        $district=request()->post('district','','strval');
        $address=request()->post('address','','strval');
        $rule=[
            'username'=>'require','mobile'=>'require',
//            'province'=>'require','city'=>'require','district'=>'require'
            'address'=>$address
        ];
        $msg=['username'=>'收货人姓名','mobile'=>'收货人手机号','address'=>'收货地址'];
        $data=['username'=>$username,'mobile'=>$mobile,'address'=>$address,'province'=>$province,'city'=>$city,'district'=>$district];
        $validate=new \think\Validate($rule,[],$msg);
        if (!$validate->check($data)){
            $this->error($validate->getError());
        }else{
            if (empty(\app\common\model\UserAddress::get(['uid'=>$this->auth->id]))){
                $data['isdefault']=1;
            }
            $data['uid']=$this->auth->id;
            $info=\app\common\model\UserAddress::create($data);
            if ($info){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }
    }

    /**
     * 编辑收货地址
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="地址id")
     * @ApiParams   (name="username", type="string", required=true, description="收货人姓名")
     * @ApiParams   (name="mobile", type="string", required=true, description="手机号")
     * @ApiParams   (name="province", type="string", required=false, description="省、自治区")
     * @ApiParams   (name="city", type="string", required=false, description="城市")
     * @ApiParams   (name="district", type="string", required=false, description="区县")
     * @ApiParams   (name="address", type="string", required=true, description="详细地址")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 17:34
     */
    public function edit()
    {
        $id=request()->post('id');
        $username=request()->post('username');
        $mobile=request()->post('mobile');
        $province=request()->post('province');
        $city=request()->post('city');
        $district=request()->post('district');
        $address=request()->post('address');
        $rule=[
            'id'=>'require|number',
            'username'=>'require','mobile'=>'require',
//            'province'=>'require','city'=>'require','district'=>'require'
            'address'=>$address
        ];
        $msg=['username'=>'收货人姓名','mobile'=>'收货人手机号','address'=>'收货地址'];
        $data=['id'=>$id,'username'=>$username,'mobile'=>$mobile,'address'=>$address,'province'=>$province,'city'=>$city,'district'=>$district];
        $validate=new \think\Validate($rule,[],$msg);
        if (!$validate->check($data)){
            $this->error($validate->getError());
        }else{
            if (empty(\app\common\model\UserAddress::get(['uid'=>$this->auth->id,'id'=>$id]))){
                $this->error('无权限修改该地址');
            }
            $info=\app\common\model\UserAddress::update($data,['id'=>$id]);
            if ($info){
                $this->success('更新成功');
            }else{
                $this->error('更新失败');
            }
        }
    }


    /**
     *  设置默认地址
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="地址id")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 17:34
     */
    public function set_default()
    {
        $id=$this->request->post('id');
        if (empty($id)){$this->error('参数错误');}
        $check=\app\common\model\UserAddress::get(['uid'=>$this->auth->id,'id'=>$id,'isdelete'=>0]);
        if ($check){
            if (\app\common\model\UserAddress::update(['isdefault'=>0],['uid'=>$this->auth->id])){
                $info=\app\common\model\UserAddress::update(['isdefault'=>1],['id'=>$id]);
                if ($info){
                    $this->success('设置成功');
                }else{
                    $this->error('设置失败');
                }
            }
        }else{
            $this->error('地址不存在');
        }
    }
    /**
     *  删除地址
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="地址id")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 17:34
     */
    public function set_delete()
    {
        $id=$this->request->post('id');
        if (empty($id)){$this->error('参数错误');}
        $check=\app\common\model\UserAddress::get(['uid'=>$this->auth->id,'id'=>$id,'isdelete'=>0]);
        if ($check){
            $info=\app\common\model\UserAddress::update(['isdelete'=>1],['id'=>$id]);
            if ($info){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('地址不存在或已删除');
        }
    }
}