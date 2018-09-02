<?php

namespace app\admin\controller\camera;

use app\common\controller\Backend;

/**
 * 摄像头管理
 *
 * @icon fa fa-camera
 */
class Camera extends Backend
{
    
    /**
     * Camera模型对象
     * @var \app\admin\model\Camera
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Camera');
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function selectpage()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'htmlspecialchars']);

        //搜索关键词,客户端输入以空格分开,这里接收为数组
        $word = (array)$this->request->request("q_word/a");
        //当前页
        $page = $this->request->request("pageNumber");
        //分页大小
        $pagesize = $this->request->request("pageSize");
        //搜索条件
        $andor = $this->request->request("andOr", "and", "strtoupper");
        //排序方式
        $orderby = (array)$this->request->request("orderBy/a");
        //显示的字段
        $field = $this->request->request("showField");
        //主键
        $primarykey = $this->request->request("keyField");
        //主键值
        $primaryvalue = $this->request->request("keyValue");
        //搜索字段
        $searchfield = (array)$this->request->request("searchField/a");
        //自定义搜索条件
        $custom = (array)$this->request->request("custom/a");
        $order = [];
        foreach ($orderby as $k => $v) {
            $order[$v[0]] = $v[1];
        }
        $field = $field ? $field : 'name';
        $this->model=model('Camera');

        $where['status']=1;
        $list = [];
        $total = $this->model->where($where)->count();
        if ($total > 0) {

            $datalist = $this->model->where($where)
                ->order('id desc')
                ->page($page, $pagesize)
                ->field('id,deviceSerial,deviceName')
                ->select();
//            dump($datalist);exit();
            foreach ($datalist as $index => $item) {
                unset($item['password'], $item['salt']);
                $item['display_deviceSerial']=$item['deviceName'].'-'.$item['deviceSerial'];
                $list[] = [
                    $primarykey => isset($item[$primarykey]) ? $item[$primarykey] : '',
                    $field      => isset($item[$field]) ? $item[$field] : ''
                ];
            }
        }
        //这里一定要返回有list这个字段,total是可选的,如果total<=list的数量,则会隐藏分页按钮
        return json(['list' => $list, 'total' => $total]);
    }

}
