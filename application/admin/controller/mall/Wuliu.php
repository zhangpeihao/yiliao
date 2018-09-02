<?php

namespace app\admin\controller\mall;

use app\common\controller\Backend;

/**
 * 商品物流信息管理
 *
 * @icon fa fa-circle-o
 */
class Wuliu extends Backend
{
    
    /**
     * MallWuliu模型对象
     * @var \app\admin\model\MallWuliu
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('MallWuliu');
        $this->view->assign("statusList", $this->model->getStatusList());

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function add()
    {
        if (input('out_trade_no')){
            $this->view->assign('out_trade_no',input('out_trade_no'));
        }else{
            $this->view->assign('out_trade_no','');
        }
        return parent::add();
    }

    

}
