<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/24
 * Time: 15:55
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 商品列表
 * Class MallProduct
 * @package app\api\controller
 */
class MallProduct extends Api{
    protected $noNeedLogin=['get_list','get_detail'];
    protected $noNeedRight='*';

    /**
     * 查询商品列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="type", type="int", required=false, description="商品类型：1乐器与部件，2.课程，3积分商城")
     * @ApiParams   (name="cid", type="int", required=false, description="商品分类id")
     * @ApiParams   (name="title", type="string", required=false, description="商品标题")
     * @ApiParams   (name="min_price", type="float", required=false, description="价格区间最小值")
     * @ApiParams   (name="max_price", type="float", required=false, description="价格区间最大值")
     * @ApiParams   (name="page", type="int", required=false, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=false, description="分页大小")
     * @ApiReturnParams (name="id", type="int", required=true, description="商品id")
     * @ApiReturnParams (name="cid", type="int", required=true, description="分类id")
     * @ApiReturnParams (name="title", type="string", required=true, description="标题")
     * @ApiReturnParams (name="category_text", type="string", required=true, description="分类")
     * @ApiReturnParams (name="content", type="string", required=true, description="内容")
     * @ApiReturnParams (name="price", type="float", required=true, description="价格")
     * @ApiReturnParams (name="credit", type="float", required=true, description="金币")
     * @ApiReturnParams (name="logo", type="url", required=true, description="封面")
     * @ApiReturnParams (name="pics", type="array", required=true, description="详情图片")
     * @ApiReturnParams (name="post_fee", type="float", required=true, description="邮费")
     * @ApiReturnParams (name="remark", type="string", required=true, description="备注")
     * @ApiReturnParams (name="product_menu", type="array", required=true, description="商品规格：id套餐id，title规格名称，price规格的售价，credit所需积分数")
     * @ApiReturnParams (name="lesson_count", type="int", required=true, description="课节数")
     * @ApiReturnParams (name="level_discount", type="int", required=true, description="是否会员折扣：1会员折扣，0会员不折扣")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1527152222','data':{'total':1,'per_page':15,'current_page':1,'last_page':1,'data':[{'id':1,'agency_id':1,'cid':2,'title':'尤克里里','summary':'','logo':'https:\/\/music.588net.com\/uploads\/20180524\/7082dc1097085bd5133a73386653ae7f.png','pics':['https:\/\/music.588net.com\/uploads\/20180524\/7082dc1097085bd5133a73386653ae7f.png'],'content':'<p>简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介简介<\/p>','post_fee':'0.00','attr_id':'','extra_attr':'','lat':'','lng':'','address':'','price':'20.00','origin_price':'0.00','credit':100,'store':0,'sale_num':0,'limit':0,'transfer':'','sale_total':0,'begin_time':1527151907,'end_time':1527151907,'is_recommend':0,'status':1,'sort':0,'remark':'','kefu':'','ctime':'1970-01-01 08:00','utime':'1970-01-01 08:00','category_text':'尤克里里'}]}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 16:24
     */
    public function get_list()
    {
        $type=$this->request->request('type',1,'intval');
//        $cid=$this->request->request('cid',0,'intval');
        $title=$this->request->request('title','','strval');
        $min_price=$this->request->request('min_price',0,'floatval');
        $max_price=$this->request->request('max_price',0,'floatval');
        $lat=$this->request->request('lat','','strval');
        $lng=$this->request->request('lng','','strval');
        $page=$this->request->request('page',1,'intval');
        $page_size=$this->request->request('page_size',20,'intval');
        $map=[];
        if ($type){$map['type']=$type;}
//        if ($cid){$map['cid']=$cid;}
        if ($title){$map['title']=['like','%'.$title.'%'];}
        if ($min_price){$map['price']=['between',[$min_price,$max_price]];}
        $map['status']=1;
        if (!empty($this->auth)){
            $map['agency_id']=$this->auth->agency_id;
        }
        $data=model('MallProduct')->where($map)
//            ->order('sort asc,id desc')
            ->order(['sort'=>0,'sort'=>'asc','is_recommend'=>'desc','id'=>'desc'])
            ->paginate($page_size,[],['page'=>$page])
            ->jsonSerialize();
        $this->success('查询成功',$data);
    }

    /**
     * 查询商品详情
     * @ApiMethod   (GET)
     * @ApiParams   (name="id", type="int", required=true, description="商品id")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/5/24 16:25
     */
    public function get_detail()
    {
        $id=$this->request->request('id',0,'intval');
        if (empty($id)){$this->error('参数错误');}
        $data=model('MallProduct')->find($id);
        $this->success('查询成功',$data);
    }
}