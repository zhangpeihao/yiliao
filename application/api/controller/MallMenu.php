<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/24
 * Time: 17:47
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 产品套餐(暂时不用)
 * Class MallMenu
 * @package app\api\controller
 */
class MallMenu extends Api{
    protected $noNeedRight='*';

    protected $noNeedLogin=[];

    /**
     * 商品套餐列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="pid", type="int", required=true, description="商品id")
     * @ApiReturnParams   (name="id", type="int", required=true, description="规格id")
     * @ApiReturnParams   (name="title", type="string", required=true, description="规格名称")
     * @ApiReturnParams   (name="price", type="string", required=true, description="规格的售价")
     * @ApiReturnParams   (name="credit", type="string", required=true, description="所需积分数")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 17:55
     */
    public function get_list()
    {
        $pid=request()->post('pid',0,'intval');
        if (empty($pid)){$this->error('请指定商品');}
        $data=model('MallProductMenu')->where('pid',$pid)->where('status',1)->order('id desc')->select();
        $this->success('查询成功',$data);
    }

}