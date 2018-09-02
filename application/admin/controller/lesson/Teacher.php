<?php

namespace app\admin\controller\lesson;

use app\common\controller\Backend;

/**
 * 教学老师
 *
 * @icon fa fa-circle-o
 */
class Teacher extends Backend
{
    
    /**
     * Teacher模型对象
     */
    protected $model = null;

    protected $searchFields='id,username,mobile,is_bind,status,createtime';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Teacher');
        $this->view->assign("isBindList", $this->model->getIsBindList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function index()
    {
        $nodeList = \app\admin\model\Teacher::get_power_list();
        $this->assign('power_list',$nodeList);
        return parent::index();
    }
    public function add()
    {
        $nodeList = \app\admin\model\Teacher::getTreeList();
        $this->assign("nodeList", $nodeList);
        return parent::add();
    }

    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $rules = explode(',', $row['power']);
        $nodeList = \app\admin\model\Teacher::getTreeList($rules);
        $this->assign("nodeList", $nodeList);
        return parent::edit($ids);
    }

    public function selectpage()
    {
        return parent::selectpage();
    }
}
