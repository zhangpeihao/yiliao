<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 11:37
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 用户角色权限
 * Class UserRole
 * @package app\api\controller
 */
class UserRole extends Api{

    protected $noNeedRight='*';
    /**
     * 获取用户权限列表
     * @ApiMethod   (POST)
     * @ApiParams   (name="group_id", type="int", required=true, description="角色id：2普通会员，4教师,5教务")
     * @ApiReturn (data="{'code':1,'msg':'操作成功','time':'1522814646','data':[{'id':17,'name':'teacher','title':'教师'},{'id':16,'name':'student','title':'学员'},{'id':15,'name':'lesson','title':'课程'},{'id':14,'name':'class_room','title':'教室管理'},{'id':13,'name':'banji','title':'班级'},{'id':4,'name':'user','title':'会员模块'}]}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function get_list()
    {
        $group_id=$this->request->request('group_id');
        if ($group_id){
            $map['id']=$group_id;
        }else{
            $map['id']=$this->auth->group_id;
        }
        $user_group_role=model('UserGroup')->where($map)->value('rules');
        $map['id']=['in',$user_group_role];
        $data=model('UserRule')->where('pid',2)->where('ismenu',0)
            ->where($map)
            ->order('weigh desc')->field('id,name,title')->select();
        $this->success('操作成功',$data);
    }
}