<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Db;
use think\Debug;
use think\Env;

/**
 * 首页接口
 */
class Index extends Api
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     * 
     */
    public function index()
    {
        $res=[];
//        $res=Db::name('user')->field('_id',true)->select();
//        $res2=Db::name('member')->insertAll($res);
//        dump($res2);
//        dump(Env::get('mongo'));exit();
        $this->success('请求成功',$res);
    }


    public function auto_make_shedule()
    {
        $banji_lesson=db('banji_lesson')->where('status',1)->select();
        foreach ($banji_lesson as $v){
            //根据该班课学员来生成课程
            $banji_student=db('banji_student')->where(['banji_lesson_id'=>$v['id'],'status'=>1])
                ->where('type',1)->where('student_id','gt',0)->column('student_id');
            $v['banji_lesson_id']=$v['id'];
            $res=\app\common\model\Shedule::auto_shedule($v,$v['creator'],$banji_student);
//            dump($banji_student);
//            dump(db('')->getLastSql());
            echo "auto_shedule";
            dump($res);
            $banji_line_student=db('banji_student')->where(['banji_lesson_id'=>$v['id'],'status'=>1])
                ->where('type',2)->where('student_id','gt',0)->select();
            foreach ($banji_line_student as $v){
                $info2=\app\common\model\Shedule::copy_shedule($v['student_id'],$v['id'],$v['shedule_id']);
                echo "copy_shedule";
                dump($info2);
            }

        }
    }
}
