<?php

namespace app\admin\controller;

use app\common\controller\Backend;

use think\Controller;
use think\Request;
use think\Validate;

/**
 * 版本管理
 *
 * @icon fa fa-circle-o
 */
class Version extends Backend
{

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Version');
    }

    public function add()
    {
        if ($this->request->isPost()){
            $data=$this->request->post('row/a');
            $rule=[
                'oldversion'=>'require',
                'newversion'=>'require',
                'packagesize'=>'require',
                'content'=>'require',
                'platform'=>'require'
            ];
            $msg=[
                'oldversion'=>'旧版本号','newversion'=>'新版本号','content'=>'版本内容','packagesize'=>'包大小','platform'=>'平台'
            ];
            $validate=new Validate($rule,[],$msg);
            if (!$validate->check($data)){
                $this->error($validate->getError());
            }
//            if (!strstr($data['downloadurl'],'http')){
//
//            }
            $data['downloadurl']=config('api_url').$data['local'];
            $info=\app\common\model\Version::create($data,true);
            if ($info){
                $this->success('添加成功');
            }else{
                $this->error('操作失败');
            }
        }else{
            return parent::add();
        }
    }

    public function edit($ids="")
    {
        if ($this->request->isPost()){
            $data=$this->request->post('row/a');
            $rule=[
                'oldversion'=>'require',
                'newversion'=>'require',
                'packagesize'=>'require',
                'content'=>'require',
                'platform'=>'require'
            ];
            $msg=[
                'oldversion'=>'旧版本号','newversion'=>'新版本号','content'=>'版本内容','packagesize'=>'包大小','platform'=>'平台'
            ];
            $validate=new Validate($rule,[],$msg);
            if (!$validate->check($data)){
                $this->error($validate->getError());
            }
            if (!strstr($data['downloadurl'],'http')){
                $data['downloadurl']=config('api_url').$data['downloadurl'];
            }
            $info=\app\common\model\Version::update($data,['id'=>$data['id']]);
            if ($info){
                $this->success('更新成功');
            }else{
                $this->error('更新失败');
            }
        }else{
            return parent::edit($ids);
        }
    }
}
