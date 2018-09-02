<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/8/1
 * Time: 13:59
 */
namespace app\api\controller\backend;

use app\common\controller\Api;
use app\common\model\Student;
use app\common\model\User;

/**
 * 教务端学员等级管理
 * Class UserLevel
 * @package app\api\controller\backend
 */
class UserLevel extends Api
{
    protected $noNeedRight='*';

    protected $noNeedLogin='';


    /**
     * 教务端获取等级列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="", type="string", required=true, description="")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/8/1 14:04
     */
    public function get_list()
    {
        $map['agency_id']=$this->auth->agency_id;
        $data=model('UserLevel')->field('id,level,levelname,discount')->where($map)->order('level desc')->select();
        $this->success('查询成功',$data);
    }

    /**
     * 教务端新增、修改等级数据
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="列表id，")
     * @ApiParams   (name="levelname", type="int", required=true, description="等级名称")
     * @ApiParams   (name="discount", type="float", required=true, description="折扣，小于0的两位小数")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/8/1 14:05
     */
    public function save_data()
    {
//        $level=request()->post('level',0,'intval');
        $id=request()->post('id',0,'intval');
        $levelname=request()->post('levelname','','strval');
        $discount=request()->post('discount',0,'floatval');
        if (empty($levelname)||empty($discount)){
            $this->error('请完善等级设置信息');
        }
        $agency_id=$this->auth->agency_id;
        if ($id){
            $info=\app\common\model\UserLevel::update(['levelname'=>$levelname,'discount'=>$discount],['id'=>$id]);
        }else{
            $info=\app\common\model\UserLevel::create(['agency_id'=>$agency_id,'levelname'=>$levelname,'discount'=>$discount,'creator'=>$this->auth->id]);
        }
        if ($info){
            $this->success('保存成功');
        }else{
            $this->error('保存失败');
        }
    }

    /**
     * 修改学员等级
     * @ApiMethod   (POST)
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiParams   (name="level_id", type="int", required=true, description="等级id")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/8/1 15:14
     */
    public function set_student_level()
    {
        $student_id=request()->post('student_id',0,'intval');
        $level_id=request()->post('level_id',0,'intval');
        $check=db('student')->find($student_id);
        if (empty($check)){
            $this->error('学员不存在');
        }
        $check_level=db('user_level')->where('id',$level_id)->where('agency_id',$this->agency_id)->find();
        if (empty($check_level)){
            $this->error('该等级不属于贵机构');
        }
        $info=Student::update(['level'=>$level_id],['id'=>$student_id]);
        if ($info){
            User::update(['level'=>$level_id],['mobile'=>$check_level['mobile'],'status'=>1]);
            $this->success('更新成功');
        }else{
            $this->error('更新失败');
        }
    }
}