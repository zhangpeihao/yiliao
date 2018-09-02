<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 学员管理
 *
 * @icon fa fa-circle-o
 */
class Student extends Backend
{
    
    /**
     * Student模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Student');
        $this->model->where('status','neq',0);
        $this->view->assign("genderList", $this->model->getGenderList());
        $this->view->assign("learnStatusList", $this->model->getLearnStatusList());
        $this->view->assign("statusList", $this->model->getStatusList());
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

    public function import()
    {
        $file = $this->request->request('file');
        if (!$file)
        {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $file;
        if (!is_file($filePath))
        {
            $this->error(__('No results were found'));
        }
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($filePath))
        {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath))
            {
                $PHPReader = new \PHPExcel_Reader_CSV();
                if (!$PHPReader->canRead($filePath))
                {
                    $this->error(__('Unknown data format'));
                }
            }
        }

        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);
        foreach ($list as $k => $v)
        {
            if ($importHeadType == 'comment')
            {
                $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
            }
            else
            {
                $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
            }
        }

        $PHPExcel = $PHPReader->load($filePath); //加载文件
        $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
        $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
        $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
        $maxColumnNumber = \PHPExcel_Cell::columnIndexFromString($allColumn);
        for ($currentRow = 1; $currentRow <= 1; $currentRow++)
        {
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++)
            {
                $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                $fields[] = $val;
            }
        }
        $insert = [];
        for ($currentRow = 2; $currentRow <= $allRow; $currentRow++)
        {
            $values = [];
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++)
            {
                $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                $values[] = is_null($val) ? '' : $val;
            }
            $row = [];
            $temp = array_combine($fields, $values);
            foreach ($temp as $k => $v)
            {
                if (isset($fieldArr[$k]) && $k !== '')
                {
                    if ($fieldArr[$k]=='birthday' && !empty($v)){
                        $v=\PHPExcel_Shared_Date::ExcelToPHP($v);
                        $v=date('Y-m-d',$v);
                    }
                    if ($fieldArr[$k]=='gender'){
                        if ($v=='男'){
                            $v=1;
                        }elseif ($v=='女'){
                            $v=2;
                        }
                    }
                    if ($fieldArr[$k]=='learn_status'){
                        if ($v=='在读'){
                            $v=1;
                        }elseif ($v=='试听'){
                            $v=2;
                        }elseif($v=='过期'){
                            $v=3;
                        }
                    }
                    $row[$fieldArr[$k]] = $v;
                }
            }
            if ($row)
            {
                if (empty(array_filter($row))){
                    continue;
                }
                $row['creator']=$this->auth->id;
                $row['status']=1;
                if ($this->model->where(['agency_id'=>$row['agency_id'],'mobile'=>$row['mobile']])){
                    $this->error('该学员已存在');
                }
                $insert[] = $row;
                $result=$this->validate($row,'Student.add');
                if (true!==$result){
                    $this->error($result);
                }
            }
        }
        if (!$insert)
        {
            $this->error(__('No rows were updated'));
        }
        try
        {
            $this->model->saveAll($insert);
        }
        catch (\think\exception\PDOException $exception)
        {
            $this->error($exception->getMessage());
        }

        $this->success();
    }
}
