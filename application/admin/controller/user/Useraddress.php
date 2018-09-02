<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Useraddress extends Backend
{
    
    /**
     * UserAddress模型对象
     * @var \app\admin\model\UserAddress
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('UserAddress');
        $this->view->assign("isdefaultList", $this->model->getIsdefaultList());
        $this->view->assign("isdeleteList", $this->model->getIsdeleteList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function add()
    {
        if ($this->request->isPost()){
            $data=request('row/a');
            $city=explode('/',$data['city']);
            $data['province']=$city[0];
            $data['city']=$city[1];
            $data['district']=$city[2];
            $info=\app\admin\model\UserAddress::create($data);
            if ($info){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }else{
            return parent::add();
        }
    }
    public function edit($ids='')
    {
        if ($this->request->isPost()){
            $data=$this->request->post("row/a", []);
            $city=explode('/',$data['city']);
            $data['province']=$city[0];
            $data['city']=$city[1];
            $data['district']=$city[2];
            $info=\app\admin\model\UserAddress::update($data,['id'=>$this->request->param('ids')]);
            if ($info){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }else{
            return parent::edit($ids);
        }
    }

}
