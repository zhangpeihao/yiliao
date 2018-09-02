<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/24
 * Time: 18:41
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 产品规格
 * Class MallAttr
 * @package app\api\controller
 */
class MallAttr extends Api{
    protected $noNeedRight='*';
    protected $noNeedLogin='*';

    /**
     * 查询产品属性规格
     * @ApiMethod   (GET)
     * @ApiParams   (name="pid", type="int", required=true, description="产品id")
     * @ApiReturnParams   (name="id", type="string", required=true, description="属性id")
     * @ApiReturnParams   (name="attr_name", type="string", required=true, description="属性名称")
     * @ApiReturnParams   (name="attr_value", type="array", required=true, description="属性选项：传给服务端取里边的key")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1527159023','data':[{'id':41,'pid':1,'attr_name':'产品规格','input_type':'','attr_value':[{'key':1,'value':'大号'},{'key':2,'value':'小号'},{'key':3,'value':'中号'}],'remark':'','product_title':'尤克里里'}]}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 18:41
     */
    public function get_attr_list()
    {
        $pid=$this->request->request('pid',0,'intval');
        if (empty($pid)){$this->error('请指定产品');}
        $data=model('MallProductAttr')->where('pid',$pid)->order('id desc')->select();
        $this->success('查询成功',$data);
    }
}