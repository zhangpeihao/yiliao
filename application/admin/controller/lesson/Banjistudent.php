<?php

namespace app\admin\controller\lesson;

use app\common\controller\Backend;

/**
 * 班级学员列管理
 *
 * @icon fa fa-circle-o
 */
class Banjistudent extends Backend
{
    
    /**
     * BanjiStudent模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('BanjiStudent');
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->assign('banji_lesson_id',input('banji_lesson_id',0));
        $this->assign('shedule_id',input('ids',0));
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function index()
    {
        if (request()->isAjax() && input('shedule_id')!='{}'){
            list($map, $sort, $order, $offset, $limit) = $this->buildparams();
            $banji_lesson_id=input('banji_lesson_id');
            $student_list=db('shedule')->where('banji_lesson_id',$banji_lesson_id)->column('student_id');
            $where['id']=['in',$student_list];
//            $where['banji_lesson_id']=$banji_lesson_id;
            $total = model('student')
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = model('student')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return parent::index();
    }
    

}
