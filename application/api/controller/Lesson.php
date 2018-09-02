<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/3 14:44
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 系统课程
 * Class Lesson
 * @package app\api\controller
 */
class Lesson extends Api{
    protected $noNeedRight='*';
    /**
     * 获取系统课程列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="page", type="int", required=true, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=true, description="每页数据条数")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1522738417','data':[{'id':1,'name':'尤克里里弹唱初级16课','status':1,'creator':1,'createtime':1522737786,'updatetime':1522737786},{'id':2,'name':'爵士鼓初级16课','status':1,'creator':1,'createtime':1522737798,'updatetime':1522737798},{'id':3,'name':'民谣弹唱初级16课','status':1,'creator':1,'createtime':1522737827,'updatetime':1522737827}]}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function get_list()
    {
        $page=$this->request->request('page',1);
        $page_size=$this->request->request('page_size',20,'intval');
        $map=['status'=>1,'agency_id'=>$this->auth->agency_id];
        $data=model('Lesson')->where($map)->order('id desc')->paginate($page_size,[],['page'=>$page])->jsonSerialize();
        $this->success('查询成功',$data['data']);
    }

    /**
     * 添加课程
     * @ApiMethod   (POST)
     * @ApiParams   (name="name", type="string", required=true, description="课程名称")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function add_lesson()
    {
        $name=$this->request->post('name','');
        if (empty($name)){$this->error('请输入课程名称');}
        if (\app\common\model\Lesson::get(['name'=>$name,'agency_id'=>$this->auth->agency_id])){
            $this->error('该课程名称已存在');
        }
        $info=\app\admin\model\Lesson::create(['agency_id'=>$this->auth->agency_id,'name'=>$name,'status'=>1,'creator'=>$this->auth->id]);
        if ($info){
            $this->success('添加成功',['lesson_id'=>$info->getLastInsID()]);
        }else{
            $this->error('添加失败');
        }
    }

    /**
     * 删除课程
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="课程id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function delete_lesson()
    {
        $id=$this->request->post('id',0);
        if (empty($id)){$this->error('请指定课程');}
        $check=\app\common\model\Lesson::get($id);
        if (empty($check)){$this->error('课程不存在');}
        if ($check['agency_id']!=$this->auth->agency_id){
            $this->error('无权限删除其他机构课程');
        }
        $info=\app\common\model\Lesson::update(['status'=>0],['id'=>$id]);
        if ($info){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    /**
     * 编辑课程
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="课程id")
     * @ApiParams   (name="name", type="string", required=true, description="课程名称")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function edit_lesson()
    {
        $name=$this->request->post('name');
        $id=$this->request->post('id');
        if (empty($name)||empty($id)){$this->error('参数错误');}
        $check=\app\common\model\Lesson::get($id);
        if (empty($check)){$this->error('课程不存在');}
        if ($check['agency_id']!=$this->auth->agency_id){
            $this->error('无权限修改其他机构课程');
        }
        $info=\app\common\model\Lesson::update(['name'=>$name],['id'=>$id]);
        if ($info){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
}