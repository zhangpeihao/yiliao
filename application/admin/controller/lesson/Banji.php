<?php

namespace app\admin\controller\lesson;

use app\admin\model\User;
use app\common\controller\Backend;

/**
 * 学员班级
 *
 * @icon fa fa-circle-o
 */
class Banji extends Backend
{
    
    /**
     * Banji模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Banji');
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign('header_uid',model('teacher')->where(['type'=>3])->column('username','id'));
        $this->view->assign('lesson',model('lesson')->where(['status'=>1])->column('name','id'));
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function selectpage()
    {
        return parent::selectpage();
    }

}
