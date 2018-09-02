<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/27
 * Time: 12:44
 */
namespace app\api\controller\backend;

use app\common\controller\Api;

/**
 * 教务端管理商品规格
 * Class MallProductAttr
 * @package app\api\controller\backed
 */
class MallProductAttr extends Api
{
    protected $noNeedRight='*';

    protected $noNeedLogin='*';

    /**
     * 获取商品规格列表
     * @ApiMethod   (POST)
     * @ApiParams   (name="product_id", type="int", required=true, description="商品id")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/27 15:02
     */
    public function get_list()
    {
        $product_id=request()->post('product_id',0,'intval');
        if (empty($product_id)){$this->error('请指定商品');}
        $data=\app\common\model\MallProductAttr::get(['pid'=>$product_id]);
        $this->success('查询成功',$data);
    }

    /**
     * 新增、更新商品规格
     * @ApiMethod   (POST)
     * @ApiParams   (name="product_id", type="int", required=true, description="商品id")
     * @ApiReturnParams   (name="attr_value", type="string", required=true, description="规格，多个用英文逗号拼接")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/27 15:03
     */
    public function update_attr()
    {
        $product_id=request()->post('product_id',0,'strval');
        $attr_value=request()->post('attr_value','','strval');
        if (empty($product_id)){$this->error('请指定商品');}
        if (empty($attr_value)){$this->error('请填写规格');}
        $check=\app\common\model\MallProductAttr::get(['pid'=>$product_id,'attr_name'=>'产品规格']);
        $data=[
            'pid'=>$product_id,'attr_name'=>'产品规格','attr_value'=>$attr_value
        ];
        if (empty($check)){
            $info=\app\common\model\MallProductAttr::create($data);
        }else{
            $info=\app\common\model\MallProductAttr::update($data,['id'=>$check['id']]);
        }
        if ($info){
            $this->success('更新成功',$data);
        }else{
            $this->error('更新失败');
        }
    }
}