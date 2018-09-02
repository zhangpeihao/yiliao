<?php

namespace app\admin\controller\lesson;

use app\common\controller\Backend;

/**
 * 班级课程管理
 *
 * @icon fa fa-circle-o
 */
class Banjilesson extends Backend
{
    
    /**
     * BanjiLesson模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('BanjiLesson');
        $this->view->assign("frequencyList", $this->model->getFrequencyList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign('creator',$this->auth->id);
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function selectpage()
    {

//        return model('BanjiLesson')->where('banji_id');
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
        $data=model('banji_lesson')->where('status',1)->order('id desc')->paginate($pagesize,[],['page'=>$page])->jsonSerialize();

        return json(['list'=>$data['data'],'total'=>$data['total']]);
    }

}
