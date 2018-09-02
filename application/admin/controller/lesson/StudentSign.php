<?php

namespace app\admin\controller\lesson;

use app\common\controller\Backend;

/**
 * 学员签到
 *
 * @icon fa fa-circle-o
 */
class StudentSign extends Backend
{
    
    /**
     * StudentSign模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('StudentSign');
        $this->view->assign('StudentList',$this->model->getStudentList());
        $this->view->assign('LessonList',$this->model->getLessonList());
        $this->view->assign('ClassRoomList',$this->model->getClassRoomList());
        $this->view->assign("decLessonList", $this->model->getDecLessonList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign('creator',$this->auth->id);
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
