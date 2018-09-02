<?php

namespace app\admin\controller\shedule;

use app\common\controller\Backend;

/**
 * 课程安排
 *
 * @icon fa fa-circle-o
 */
class Shedule extends Backend
{
    
    /**
     * Shedule模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Shedule');
        $this->view->assign("weekList", $this->model->getWeekList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function index()
    {
        if (request()->isAjax()){
            list($map, $sort, $order, $offset, $limit) = $this->buildparams();
            $where=(array)$map;
            $where['status']=['gt',0];
            $total = model('shedule')
                ->where($where)
                ->order($sort, $order)
//                ->group('agency_id,banji_lesson_id,lesson_id,begin_time,end_time')
                ->count();

            $list = model('shedule')
                ->where($where)
                ->order($sort, $order)
//                ->group('agency_id,banji_lesson_id,lesson_id,begin_time,end_time')
                ->limit($offset, $limit)
                ->select();
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return parent::index();
    }
    
    public function selectpage()
    {
//        return parent::selectpage();
        //设置过滤方法
        $this->request->filter(['strip_tags', 'htmlspecialchars']);

        //搜索关键词,客户端输入以空格分开,这里接收为数组
        $word = (array) $this->request->request("q_word/a");
        //当前页
        $page = $this->request->request("pageNumber");
        //分页大小
        $pagesize = $this->request->request("pageSize");
        //搜索条件
        $andor = $this->request->request("andOr");
        //排序方式
        $orderby = (array) $this->request->request("orderBy/a");
        $data=model('shedule')->where('status',1)->order('date asc')->paginate($pagesize,[],['page'=>$page])->jsonSerialize();

        return json(['list'=>$data['data'],'total'=>$data['total']]);
    }


    public function edit($ids=null)
    {
        if (request()->isAjax()){
            $data=input('row/a');
            $shedule_id=input('ids');
            $shedule_info=db('shedule')->where('id',$shedule_id)->field('banji_id,banji_lesson_id,date,begin_time,end_time')->find();
            $info=\app\common\model\Shedule::update($data,$shedule_info,true);
            if ($info){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }
        return parent::edit($ids);
    }
}
