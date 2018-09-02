<?php

namespace app\admin\controller\mall;

use app\common\controller\Backend;

/**
 * 商品信息管理
 *
 * @icon fa fa-circle-o
 */
class Product extends Backend
{
    
    /**
     * MallProduct模型对象
     * @var \app\admin\model\MallProduct
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('MallProduct');
        $this->view->assign("isRecommendList", $this->model->getIsRecommendList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign('category_type_list',model('MallCategory')->getTypeList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $where=(array)$where;
            $where['type']=1;
            $total = model('mall_product')
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list =  model('mall_product')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    public function index2()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $where=(array)$where;
            $where['type']=2;
            $total = model('mall_product')
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list =  model('mall_product')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    public function index3()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $where=(array)$where;
            $where['type']=3;
            $total = model('mall_product')
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list =  model('mall_product')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    public function add2()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill)
                {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : true) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $params['type']=2;
                    $result = $this->model->allowField(true)->save($params);
                    if ($result !== false)
                    {
                        $this->success();
                    }
                    else
                    {
                        $this->error($this->model->getError());
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }
    public function add3()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill)
                {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : true) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $params['type']=3;
                    $result = $this->model->allowField(true)->save($params);
                    if ($result !== false)
                    {
                        $this->success();
                    }
                    else
                    {
                        $this->error($this->model->getError());
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    public function self_accept($ids=null)
    {
        if ($this->request->isPost()){
            $data=input('row/a');
            $info=$this->model->where('id',$ids)->isUpdate(true)->data(['self_accept'=>$data]);
            if ($info){
                $this->success('更新成功');
            }else{
                $this->error('更新失败');
            }
        }
        $this->view->assign('self_accept',$this->model->where('id',$ids)->value('self_accept'));
        return $this->fetch();
    }

    public function edit2($ids=null)
    {
        if ($this->request->isPost()){
            return parent::edit($ids);
        }
        return $this->fetch('',['row'=>$this->model->get($ids)]);
    }
    public function edit3($ids=null)
    {
        if ($this->request->isPost()){
            return parent::edit($ids);
        }
        return $this->fetch('',['row'=>$this->model->get($ids)]);
    }

}
