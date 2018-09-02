<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/24
 * Time: 19:45
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 账户历史记录
 * Class UserScore
 * @package app\api\controller
 */
class UserScore extends Api{
    protected $noNeedRight='*';

    protected $noNeedLogin=[];

    /**
     * 获取积分变动类型，用于账户历史接口查询type字段
     * @ApiMethod   (GET)
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 19:51
     */
    public function get_type_list()
    {
        $this->success(model('UserScore')->getTypeList());
    }

    /**
     * 查询金币变动历史记录
     * @ApiMethod   (GET)
     * @ApiParams   (name="type", type="int", required=false, description="变动类型：从接口获取对应值")
     * @ApiParams   (name="operate", type="int", required=false, description="变动方式：-1减，1加")
     * @ApiParams   (name="page", type="int", required=false, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=false, description="分页大小")
     * @ApiReturnParams   (name="operate", type="string", required=true, description="变动方式1加，-1减")
     * @ApiReturnParams   (name="type", type="string", required=true, description="变动类型，参照接口")
     * @ApiReturnParams   (name="creator", type="string", required=true, description="操作人")
     * @ApiReturnParams   (name="num", type="float", required=true, description="变动数额")
     * @ApiReturnParams   (name="last_num", type="float", required=true, description="上一次值")
     * @ApiReturnParams   (name="remark", type="string", required=true, description="备注")
     * @ApiReturnParams   (name="ctime", type="string", required=true, description="创建时间")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1527162842','data':{'total':1,'per_page':15,'current_page':1,'last_page':1,'data':[{'id':1885,'uid':11,'operate':1,'type':1,'creator':'system','money':'0.00','last_money':'0.00','num':'10.00','last_num':'0.00','link_order':'','link_id':3,'remark':'发布练习视频','ctime':'2018-05-24 19:44','utime':'2018-05-24 19:44'}]}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 19:49
     */
    public function get_list()
    {
        $type=$this->request->request('type',0,'intval');
        $opreate=$this->request->request('operate',1,'intval');
        $page=$this->request->request('page',1,'intval');
        $page_size=$this->request->request('page_size',20,'intval');

        $map=[];
        if ($type){$map['type']=$type;}
        if ($opreate){$map['operate']=$opreate;}
        $map['uid']=$this->auth->id;
        $data=model('UserScore')->where($map)->paginate($page_size,[],['page'=>$page])->jsonSerialize();

        $this->success('查询成功',$data);
    }
}