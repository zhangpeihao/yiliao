<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/23
 * Time: 13:55
 */
namespace app\api\controller\backend;

use app\common\controller\Api;
use app\common\model\MallStoreAttr;
use think\Db;
use think\Validate;

/**
 * 教务端库存商品管理
 * Class MallStore
 * @package app\api\controller\backend
 */
class MallStore extends Api
{

    protected $noNeedLogin=['get_list','edit_content'];

    protected $noNeedRight='*';

    /**
     * 查询创建的库存列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="page", type="int", required=true, description="分页,默认为1")
     * @ApiParams   (name="page_size", type="int", required=true, description="分页大小，默认为10")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/27 15:06
     */
    public function get_list()
    {
        $uid=$this->auth->id;
        $page_size=request()->get('page_size',10,'intval');
        $page=request()->get('page',1,'intval');
//        $map['creator']=$uid;
        $map['status']=1;
        $map['agency_id']=$this->auth->agency_id;
        $data=model('MallStore')
            ->where($map)
            ->order('id desc')->paginate($page_size,[],['page'=>$page])
            ->jsonSerialize();
        $this->success('查询成功',$data);
    }

    /**
     * 库存商品详情
     * @ApiMethod   (GET)
     * @ApiParams   (name="id", type="int", required=true, description="库存商品id")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/23 15:52
     */
    public function get_detail()
    {
        $id=request()->get('id',0,'intval');
        if (empty($id)){$this->error('请指定库存商品');}
        $data=\app\common\model\MallStore::get(['id'=>$id]);
        $this->success('查询成功',$data);
    }

    /**
     * 添加库存商品
     * @ApiMethod   (POST)
     * @ApiParams   (name="title", type="string", required=true, description="标题")
     * @ApiParams   (name="logo", type="url", required=true, description="封面：如果不单上传，直接传详情图片第一张url")
     * @ApiParams   (name="pics", type="urls", required=true, description="详情图片，多张用英文逗号拼接")
     * @ApiParams   (name="price", type="float", required=true, description="价格")
     * @ApiParams   (name="total", type="float", required=true, description="进货总价")
     * @ApiParams   (name="attr_value", type="json_string", required=true, description="json二维数组转字符串提交，每个属性里包含字段：title规格名称、store对应库存数量")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/27 15:07
     */
    public function add()
    {
        $title=request()->post('title','','strval');
        $logo=request()->post('logo','','strval');
        $pics=request()->post('pics','','strval');
        $price=request()->post('price','','floatval');
        $total=request()->post('total','','floatval');
        $attr_value=request()->post('attr_value','','strval');
        $rule=[
           'title'=>'require','logo'=>'require','pics'=>'require','price'=>'require','total'=>'require'
        ];
        $msg=['title'=>'商品名称','price'=>'进货单价','logo'=>'封面','pics'=>'详情图片','total'=>'进货总价','attr_value'=>'商品规格'];
        $data=[
            'title'=>$title,'logo'=>$logo,'pics'=>$pics,'price'=>$price,'total'=>$total,'status'=>1,'creator'=>$this->auth->id,
            'from'=>'B端','agency_id'=>$this->auth->agency_id
        ];
        $validate=new Validate($rule,[],$msg);
        $res=$validate->check($data);
        if (!$res){
            $this->error($validate->getError());
        }
        Db::startTrans();
        $mall_product=new \app\common\model\MallStore();
        $info=$mall_product->save($data);
        if ($info){
            if ($attr_value){
                $attr_value=json_decode($attr_value,true);
                foreach ($attr_value as $k=>$v){
                    $data=[
                        'pid'=>$mall_product->id,'title'=>$v['title'],'store'=>$v['store']
                    ];
                    if (MallStoreAttr::get(['title'=>$v['title'],'pid'=>$data['pid']])){
                        $this->error('规格:“'.$v['title'].'”不能重复');
                    }
                    $info=\app\common\model\MallStoreAttr::create($data);
                }
            }else{
                Db::rollback();
                $this->error('商品规格不能为空');
            }
            $data['id']=$mall_product->id;
            Db::commit();
            $this->success('添加成功',$data);
        }else{
            Db::rollback();
            $this->error('添加失败');
        }
    }

    /**
     * 更新库存商品信息
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="商品id")
     * @ApiParams   (name="title", type="string", required=true, description="标题")
     * @ApiParams   (name="logo", type="url", required=true, description="封面：如果不单上传，直接传详情图片第一张url")
     * @ApiParams   (name="pics", type="urls", required=true, description="详情图片，多张用英文逗号拼接")
     * @ApiParams   (name="price", type="float", required=true, description="价格")
     * @ApiParams   (name="total", type="float", required=true, description="进货总价")
     * @ApiParams   (name="attr_value", type="json_string", required=true, description="json二维数组转字符串提交，每个属性里包含字段：id规格记录id、title规格名称、store对应库存")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/27 15:12
     */
    public function edit()
    {
        $id=request()->post('id','','intval');
        $title=request()->post('title','','strval');
        $logo=request()->post('logo','','strval');
        $pics=request()->post('pics','','strval');
        $price=request()->post('price','','floatval');
        $total=request()->post('total','','floatval');
        $store=request()->post('store',0,'intval');
        $attr_value=request()->post('attr_value','','strval');
        $rule=[
            'id'=>'require|gt:0',
//            'title'=>'require','logo'=>'require','pics'=>'require','content'=>'require',
//            'post_fee'=>'require','price'=>'require','credit'=>'require'
        ];
        $data=[];
        $data['id']=$id;
        $data['updator']=$this->auth->id;
        if ($title){$data['title']=$title;}
        if ($logo){$data['logo']=$logo;}
        if ($pics){$data['pics']=$pics;}
        if ($price){$data['price']=$price;}
        if ($total){$data['total']=$total;}
//            $data=[
//                'title'=>$title,'summary'=>$summary,'logo'=>$logo,'pics'=>$pics,'content'=>$content,'lesson_count'=>$lesson_count,
//                'post_fee'=>$post_fee,'address'=>$address,'price'=>$price,'level_discount'=>$level_discount,
//                'credit'=>$credit,'store'=>$store,'begin_time'=>strtotime($begin_time),'end_time'=>strtotime($end_time),'sort'=>$sort,
//            ];
        $validate=new Validate($rule);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }
        $info=\app\common\model\MallStore::update($data,['id'=>$id]);
        if ($info){
            //更新规格
            if ($attr_value){
                $attr_value=json_decode($attr_value,true);
                foreach ($attr_value as $k=>$v){
                    $data=[
                        'pid'=>$id,'title'=>$v['title'],'store'=>$v['store']
                    ];
//                    MallStoreAttr::get(['title'=>$v['title'],'pid'=>['neq',$id]])
                    $check=\db('mall_store_attr')->where('title',$v['title'])
                            ->where('pid',$id)->count();
                    if ($check>1){
                        $this->error('规格:“'.$v['title'].'”不能重复');
                    }
                    if ($check==0){
                        $info=MallStoreAttr::create($data);
                    }else{
                        $info=\app\common\model\MallStoreAttr::update($data,['id'=>$v['id']]);
                    }
                }
            }
            $this->success('更新成功');
        }else{
            $this->error('更新失败');
        }
    }

    /**
     * 删除库存商品
     * @ApiMethod   (POST)
     * @ApiParams   (name="product_id", type="int", required=true, description="商品id")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/27 15:13
     */
    public function delete()
    {
        $product_id=request()->post('product_id',0,'intval');
        if (empty($product_id)){$this->error('请指定产品id');}
        $check=\app\common\model\MallStore::get($product_id);
        if ($check['agency_id']!=$this->auth->agency_id){
            $this->error('无操作权限');
        }
        if ($check['creator']!=$this->auth->id){
            $this->error('你不是创建人，无权限修改');
        }
        $info=\app\common\model\MallStore::update(['status'=>0,'updator'=>$this->auth->id]);
        if ($info){
            $this->success('更新成功');
        }else{
            $this->error('更新失败');
        }
    }

}