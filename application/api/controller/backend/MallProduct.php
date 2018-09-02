<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/27
 * Time: 12:17
 */
namespace app\api\controller\backend;

use app\common\controller\Api;
use app\common\model\MallProductLog;
use think\Validate;

/**
 * 教务端管理商品
 * Class MallProduct
 * @package app\api\controller\backed
 */
class MallProduct extends Api{
    protected $noNeedLogin=['get_list','edit_content'];

    protected $noNeedRight='*';

    /**
     * 查询创建的商品列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="type", type="int", required=true, description="类型：1乐器与配件，2课程，3金币商城")
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
        $type=request()->get('type',1,'intval');
//        $map['creator']=$uid;
        $map['type']=$type;
        $map['agency_id']=$this->auth->agency_id;
        $data=model('MallProduct')
            ->where($map)
            ->order('id desc')->paginate($page_size,[],['page'=>$page])
            ->jsonSerialize();
        $this->success('查询成功',$data);
    }

    /**
     * 添加商品
     * @ApiMethod   (POST)
     * @ApiParams   (name="store_id", type="int", required=true, description="库存商品id")
     * @ApiParams   (name="type", type="int", required=true, description="类型：1乐器与配件，2课程，3金币商城")
     * @ApiParams   (name="title", type="string", required=true, description="标题")
     * @ApiParams   (name="summary", type="string", required=false, description="简介:可选")
     * @ApiParams   (name="logo", type="url", required=true, description="封面")
     * @ApiParams   (name="pics", type="urls", required=true, description="详情图片，多张用英文逗号拼接")
     * @ApiParams   (name="content", type="string", required=true, description="内容")
     * @ApiParams   (name="lesson_id", type="int", required=true, description="课程id")
     * @ApiParams   (name="lesson_count", type="int", required=true, description="课节数")
     * @ApiParams   (name="level_discount", type="int", required=true, description="是否会员折扣：0不折扣，1折扣")
     * @ApiParams   (name="post_fee", type="float", required=true, description="邮费")
     * @ApiParams   (name="address", type="string", required=true, description="商家地址")
     * @ApiParams   (name="price", type="float", required=true, description="价格：乐器与配件、课程需要")
     * @ApiParams   (name="credit", type="int", required=true, description="金币：只有金币商城需要")
     * @ApiParams   (name="store", type="int", required=true, description="库存")
     * @ApiParams   (name="begin_time", type="datetime", required=false, description="开始时间：可选,格式：YYYY-mm-dd HH:ii:ss")
     * @ApiParams   (name="end_time", type="datetime", required=true, description="结束时间：可选，格式：YYYY-mm-dd HH:ii:ss")
     * @ApiParams   (name="status", type="int", required=true, description="状态：1上架。0下架")
     * @ApiParams   (name="kefu", type="string", required=true, description="客服电话")
     * @ApiParams   (name="attr_value", type="string", required=true, description="规格，多个用英文逗号拼接")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/27 15:07
     */
    public function add()
    {
        $store_id=request()->post('store_id',0,'strval');
        $type=request()->post('type',1,'intval');
        $title=request()->post('title','','strval');
        $summary=request()->post('summary','','strval');
        $logo=request()->post('logo','','strval');
        $pics=request()->post('pics','','strval');
        $content=request()->post('content','','strval');
        $lesson_id=request()->post('lesson_id',0,'intval');
        $lesson_count=request()->post('lesson_count',0,'intval');
        $level_discount=request()->post('level_discount',0,'intval');
        $post_fee=request()->post('post_fee',0,'floatval');
        $address=request()->post('address','','strval');
        $price=request()->post('price','','floatval');
        $credit=request()->post('credit',0,'floatval');
        $store=request()->post('store',0,'intval');
        $begin_time=request()->post('begin_time','','strval');
        $end_time=request()->post('end_time','','strval');
        $status=request()->post('status',1,'intval');
        $kefu=request()->post('kefu','','strval');
        $attr_value=request()->post('attr_value','','strval');
        if (request()->post('id')){
            $this->edit();
        }
        $rule=[
            'type'=>'require','store_id'=>'require','title'=>'require','logo'=>'require','pics'=>'require','content'=>'require',
            'post_fee'=>'require','credit'=>'require'
        ];
        $msg=['type'=>'商品类型','store_id'=>'库存商品','title'=>'商品名称','logo'=>'封面','pics'=>'详情图片','content'=>'商品介绍',
            'post_fee'=>'运费','credit'=>'金币'];
        if ($type==3 && empty($credit)){$this->error('金币数不能为空');}
        $data=[
            'type'=>$type,'store_id'=>$store_id,
            'title'=>$title,'summary'=>$summary,'logo'=>$logo,'pics'=>$pics,'content'=>$content,'lesson_count'=>$lesson_count,'lesson_id'=>$lesson_id,
            'post_fee'=>$post_fee,'address'=>$address,'price'=>$price,'level_discount'=>$level_discount,
            'credit'=>$credit,'store'=>$store,'begin_time'=>intval(strtotime($begin_time)),'end_time'=>intval(strtotime($end_time)),
            'status'=>$status,'kefu'=>$kefu,'creator'=>$this->auth->id,'from'=>'app','agency_id'=>$this->auth->agency_id
        ];
//        dump($data);exit();
        $validate=new Validate($rule,[],$msg);
        $res=$validate->check($data);
        if (!$res){
            $this->error($validate->getError());
        }
        $mall_product=new \app\common\model\MallProduct();
        $info=$mall_product->save($data);
        if ($info){
            if ($attr_value){
                $data=[
                    'pid'=>$mall_product->id,'attr_name'=>'产品规格','attr_value'=>$attr_value
                ];
                $info=\app\common\model\MallProductAttr::create($data);
            }
            $data['id']=$mall_product->id;
            $this->success('添加成功',$data);
        }else{
            $this->error('添加失败');
        }
    }

    /**
     * 更新商品信息
     * @ApiMethod   (POST)
     * @ApiParams   (name="store_id", type="int", required=true, description="库存商品id")
     * @ApiParams   (name="type", type="int", required=false, description="类型：1乐器与配件，2课程，3金币商城")
     * @ApiParams   (name="id", type="int", required=true, description="当前商品id")
     * @ApiParams   (name="title", type="string", required=false, description="标题")
     * @ApiParams   (name="summary", type="string", required=false, description="简介:可选")
     * @ApiParams   (name="logo", type="url", required=false, description="封面")
     * @ApiParams   (name="pics", type="urls", required=false, description="详情图片，多张用英文逗号拼接")
     * @ApiParams   (name="content", type="string", required=false, description="内容")
     * @ApiParams   (name="lesson_id", type="int", required=true, description="课程id")
     * @ApiParams   (name="lesson_count", type="int", required=false, description="课程")
     * @ApiParams   (name="level_discount", type="int", required=false, description="是否会员折扣：0不折扣，1折扣")
     * @ApiParams   (name="post_fee", type="float", required=false, description="邮费")
     * @ApiParams   (name="address", type="string", required=false, description="地址")
     * @ApiParams   (name="price", type="float", required=false, description="价格：乐器与配件、课程需要")
     * @ApiParams   (name="credit", type="int", required=false, description="金币：只有金币商城需要")
     * @ApiParams   (name="store", type="int", required=false, description="库存")
     * @ApiParams   (name="begin_time", type="datetime", required=false, description="开始时间：可选,格式：YYYY-mm-dd HH:ii:ss")
     * @ApiParams   (name="end_time", type="datetime", required=false, description="结束时间：可选，格式：YYYY-mm-dd HH:ii:ss")
     * @ApiParams   (name="status", type="int", required=false, description="状态：1上架。0下架")
     * @ApiParams   (name="kefu", type="string", required=false, description="客服电话")
     * @ApiParams   (name="attr_value", type="string", required=false, description="规格，多个用英文逗号拼接")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/6/27 15:12
     */
    public function edit()
    {
        $store_id=request()->post('store_id',0,'intval');
        $id=request()->post('id','','intval');
        $title=request()->post('title','','strval');
        $summary=request()->post('summary','','strval');
        $logo=request()->post('logo','','strval');
        $pics=request()->post('pics','','strval');
        $content=request()->post('content','','strval');
        $lesson_id=request()->post('lesson_id',0,'intval');
        $lesson_count=request()->post('lesson_count',0,'intval');
        $post_fee=request()->post('post_fee',0,'floatval');
        $address=request()->post('address','','strval');
        $price=request()->post('price','','floatval');
        $level_discount=request()->post('level_discount',0,'intval');
        $credit=request()->post('credit',0,'floatval');
        $store=request()->post('store',0,'intval');
        $begin_time=request()->post('begin_time','','strval');
        $end_time=request()->post('end_time','','strval');
        $status=request()->post('status',1,'intval');
        $kefu=request()->post('kefu','','strval');
        $sort=request()->post('sort','','intval');
        $attr_value=request()->post('attr_value','','strval');
        $rule=[
            'id'=>'require|gt:0',
//            'title'=>'require','logo'=>'require','pics'=>'require','content'=>'require',
//            'post_fee'=>'require','price'=>'require','credit'=>'require'
        ];
        $data=[];
        $data['id']=$id;
        $data['updator']=$this->auth->id;
        if ($store_id){$data['store_id']=$store_id;}
        if ($title){$data['title']=$title;}
        if ($summary){$data['summary']=$summary;}
        if ($logo){$data['logo']=$logo;}
        if ($pics){$data['pics']=$pics;}
        if ($content){$data['content']=$content;}
        if ($post_fee){$data['post_fee']=$post_fee;}
        if ($address){$data['address']=$address;}
        if ($price){$data['price']=$price;}
        if ($lesson_id){$data['lesson_id']=$lesson_id;}
        if ($level_discount){$data['level_discount']=$level_discount;}
        if ($lesson_count){$data['lesson_count']=$lesson_count;}
        if ($credit){$data['credit']=$credit;}
        if ($store){$data['store']=$store;}
        if ($begin_time){$data['begin_time']=strtotime($begin_time);}
        if ($end_time){$data['end_time']=strtotime($end_time);}
        if ($sort){$data['sort']=$sort;}
        if ($status>=0){$data['status']=$status;}
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
        $info=\app\common\model\MallProduct::update($data,['id'=>$id]);
        if ($info){
            //添加记录
            $fields=array_keys($data);
            $last_info=db('mall_product')->field($fields)->find($id);
            MallProductLog::add_modify_log($data,$last_info);
            //更新规格
            if ($attr_value){
                $data=[
                    'pid'=>$id,'attr_name'=>'产品规格','attr_value'=>$attr_value
                ];
                $info=\app\common\model\MallProductAttr::update($data,['pid'=>$id,'attr_name'=>'产品规格']);
            }
            $this->success('更新成功');
        }else{
            $this->error('更新失败');
        }
    }


    public function edit_content()
    {
        return view();
    }

    /**
     * 删除商品
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
        $check=\app\common\model\MallProduct::get($product_id);
        if ($check['agency_id']!=$this->auth->agency_id){
            $this->error('无操作权限');
        }
        /*if ($check['creator']!=$this->auth->id){
            $this->error('你不是创建人，无权限修改');
        }*/
        $info=\app\common\model\MallProduct::update(['status'=>0,'updator'=>$this->auth->id]);
        if ($info){
            $this->success('更新成功');
        }else{
            $this->error('更新失败');
        }
    }
    
}