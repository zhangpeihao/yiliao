<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/3 16:27
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 教室管理
 * Class ClassRoom
 * @package app\api\controller
 */
class ClassRoom extends Api{

    protected $noNeedLogin='';
    protected $noNeedRight='*';
    /**
     * 查询教室列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="page", type="int", required=true, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=true, description="每页数据条数")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/11 14:42
     */
    public function get_list()
    {
        $page=$this->request->request('page',1);
        $page_size=$this->request->request('page_size',20,'intval');
        $data=model('ClassRoom')->where('status',1)->where('agency_id',$this->auth->agency_id)->order('id asc')->paginate($page_size,[],['page'=>$page])->jsonSerialize();
        $this->success('查询成功',$data['data']);
    }

    /**
     * 新增
     * @ApiMethod   (POST)
     * @ApiParams   (name="name", type="string", required=true, description="教室名称")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/11 14:42
     */
    public function add()
    {
        $name=$this->request->post('name','');
        if (empty($name)){$this->error('参数错误');}
        $rule=[
            'name'=>'require|unique'
        ];
        $validate=new \think\Validate($rule,[],['name'=>'教室名称']);
        $res=$validate->rule(['name'=>$name]);
        if (!$res){
            $this->error($validate->getError());
        }else{
            if (\app\common\model\ClassRoom::get(['name'=>$name])){
                $this->error('该教室名称已存在');
            }
            $data=[
                'name'=>$name,
                'creator'=>$this->auth->id,
                'status'=>1,
                'agency_id'=>$this->auth->agency_id
            ];
            $info=\app\common\model\ClassRoom::create($data);
            if ($info){
                $this->success('操作成功',['class_room'=>$info->getLastInsID()]);
            }else{
                $this->error('操作失败');
            }
        }
    }

    /**
     * 编辑
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="教室id")
     * @ApiParams   (name="name", type="int", required=true, description="教室名称")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function edit()
    {
        $id=$this->request->request('id');
        $name=$this->request->request('name');
        if (empty($id)||empty($name)){$this->error('参数错误');}
        $info=\app\common\model\ClassRoom::update(['name'=>$name],['id'=>$id]);
        if ($info){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 删除教室
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="教室id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function del()
    {
        $id=$this->request->post('id');
        if (empty($id)){$this->error('参数错误');}
        $info=\app\common\model\ClassRoom::update(['status'=>0],['id'=>$id]);
        if ($info){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
}