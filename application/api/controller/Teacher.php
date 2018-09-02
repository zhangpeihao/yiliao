<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/3 17:42
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\AgencyMember;
use app\common\model\InviteMessage;

/**
 * 教师管理
 * Class Teacher
 * @package app\api\controller
 */
class Teacher extends Api{

    protected $noNeedRight='*';
    /**
     * 获取教师列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="page", type="int", required=true, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=true, description="每页数据条数")
     * @ApiParams   (name="type", type="int", required=false, description="类型：2教务，3教师")
     * @ApiParams   (name="username", type="string", required=false, description="教师姓名")
     * @ApiParams   (name="mobile", type="string", required=false, description="教室手机号")
     * @ApiReturnParams   (name="id", type="int", required=true, description="教师 id")
     * @ApiReturnParams   (name="type", type="int", required=true, description="类型：2教务，3教师")
     * @ApiReturnParams   (name="username", type="string", required=true, description="教师姓名")
     * @ApiReturnParams   (name="mobile", type="string", required=true, description="手机号")
     * @ApiReturnParams   (name="is_bind", type="int", required=true, description="是否绑定")
     * @ApiReturnParams   (name="power_list", type="array", required=true, description="权限列表，包含字段：id权限ID，name权限标识.title权限名称")
     * @ApiReturn (data="{'code':1,'msg':'操作成功','time':'1523246784','data':{'total':1,'per_page':20,'current_page':1,'last_page':1,'data':[{'id':1,'username':'余','mobile':'15107141306','power':'20','is_bind':1,'bind_uid':11,'status':1,'createtime':1522743356,'updatetime':1522839624,'power_list':[{'id':20,'name':'dispatch','title':'请假调课'}]}],'un_join':3}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function get_list()
    {
        $page=$this->request->request('page',1);
        $page_size=$this->request->request('page_size',20);
        $username=$this->request->request('username');
        $mobile=$this->request->request('mobile');
        $type=$this->request->request('type',3,'intval');
        $map=[];
        $map['agency_id']=$this->auth->agency_id;
        if ($username){$map['username']=['like','%'.$username.'%'];}
        if ($mobile){$map['mobile']=$mobile;}
        if ($type){$map['type']=$type;}
        $map['status']=1;
        $data=model('teacher')->where($map)->order('id desc')->paginate($page_size,[],['page'=>$page])->each(function ($val,$key){
            $val['power_list']=model('user_rule')->where('id','in',$val['power'])->field('id,name,title')->order('weigh desc')->select();
            return $val;
        })->jsonSerialize();
        $un_join=model('teacher')->where(['is_bind'=>0,'status'=>1])->count();
        $data['un_join']=$un_join;
        $this->success('操作成功',$data);
    }

    /**
     * 新增教师
     * @ApiMethod   (POST)
     * @ApiParams   (name="username", type="string", required=true, description="姓名")
     * @ApiParams   (name="mobile", type="string", required=true, description="手机号")
     * @ApiParams   (name="power", type="string", required=true, description="权限：英文逗号拼接")
     * @ApiParams   (name="type", type="int", required=true, description="类型：2=>'教务',3=>'老师'")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/19 12:03
     */
    public function add()
    {
        $username=$this->request->post('username','','strval');
        $mobile=$this->request->post('mobile','','strval');
        $type=$this->request->post('type',3,'intval');
        $power=$this->request->post('power','');
        if (empty($username) ||empty($mobile)){$this->error('参数错误');}
        $info=0;
        if ($check=\app\common\model\Teacher::get(['mobile'=>$mobile,'agency_id'=>$this->auth->agency_id])){
            if ($check['status']==0){
                $info=\app\common\model\Teacher::update(['status'=>1,'type'=>$type],['id'=>$check['id']]);
                if ($info){
                    AgencyMember::update(['status'=>1],['agency_id'=>$this->auth->agency_id,'teacher_id'=>$check['id']]);
                }
            }else{
                $this->error('该教师已存在');
            }
        }else{
            if (\app\common\model\User::get(['mobile'=>$mobile])){
                $is_bind=1;
            }else{
                $is_bind=0;
            }
            $info=\app\common\model\Teacher::create([
                'username'=>$username,
                'mobile'=>$mobile,
                'power'=>$power,
                'is_bind'=>$is_bind,
                'status'=>1,
                'type'=>$type,
                'agency_id'=>$this->auth->agency_id,
                'creator'=>$this->auth->id
            ]);
        }
        if ($info){
            $this->success('操作成功',['teacher_id'=>$info->getLastInsID()]);
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 编辑教师
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="教师id")
     * @ApiParams   (name="username", type="string", required=true, description="姓名")
     * @ApiParams   (name="mobile", type="string", required=true, description="手机号,手机号不能修改，用于查询")
     * @ApiParams   (name="power", type="string", required=true, description="权限：英文逗号拼接")
     * @ApiParams   (name="type", type="int", required=true, description="类型：2=>'教务',3=>'老师'")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/19 12:03
     */
    public function edit()
    {
        $id=$this->request->post('id','','intval');
        $username=$this->request->post('username','','strval');
        $mobile=$this->request->post('mobile','','strval');
        $type=$this->request->post('type',3,'intval');
        $power=$this->request->post('power','');
        if (empty($id)|| empty($username) ||empty($mobile)){$this->error('参数错误');}
        $check=\app\common\model\Teacher::get(['mobile'=>$mobile,'agency_id'=>$this->auth->agency_id]);
        if(empty($check)){$this->error('该教师不存在');}
        if (\app\common\model\User::get(['mobile'=>$mobile])){
            $is_bind=1;
        }else{
            $is_bind=0;
        }
        $info=\app\common\model\Teacher::update([
            'username'=>$username,
            'power'=>$power,
            'is_bind'=>$is_bind,
            'type'=>$type,
        ],['id'=>$id],true);
        if ($info){
            $this->success('操作成功',['teacher_id'=>$id]);
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 删除教师
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="教师id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function del()
    {
        $id=$this->request->post('id');
        if (empty($id)){$this->error('参数错误');}
        $check=\app\common\model\Teacher::get($id);
        if (empty($check)){$this->error('教师不存在');}
        if ($check['agency_id']!=$this->auth->agency_id){
            $this->error('无权限操作');
        }
        $info=\app\common\model\Teacher::update(['status'=>0],['id'=>$id]);
        if ($info){
            AgencyMember::update(['status'=>0],['agency_id'=>$this->auth->agency_id,'teacher_id'=>$id]);
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }


    /**
     * 发送短信邀请
     *
     * @ApiMethod   (POST)
     * @ApiParams   (name="teacher_id", type="int", required=true, description="教师id")
     * @ApiReturn (data="{'code':1,'msg':'操作成功','time':'1522832197','data':{'username':'151****1306','to_username':'余','to_mobile':'15107141306','title':'151****1306邀请您加入远航音乐工作室','content':'一起来用远航音乐教务APP，提升教务管理效率，赶紧加入吧。','code':'1d35218f-32c7-433a-a22d-74ce0b1c7f92','url':'https:\/\/music.588net.com\/api\/invite\/index\/code\/1d35218f-32c7-433a-a22d-74ce0b1c7f92.html','status':0,'creator':11}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function send_invite()
    {
        $teacher_id=$this->request->post('teacher_id');
        $msg=new InviteMessage();
        if ($res=$msg->create_msg($this->auth,$teacher_id)){
            $this->success('操作成功',$res);
        }else{
            $this->error('邀请失败');
        }
    }
}