<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 12:13
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\ContractRecord;
use app\common\model\Refund;
use think\Db;

/**
 * 合约管理
 * Class Contract
 * @package app\api\controller
 */
class Contract extends Api{

    protected $noNeedRight='*';

    /**
     * 新增合约
     * @ApiMethod   (POST)
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiParams   (name="startdate", type="int", required=true, description="签约日期：传YYYY-mm-dd")
     * @ApiParams   (name="enddate", type="int", required=true, description="有效期（传YYYY-mm-dd）")
     * @ApiParams   (name="lesson_id", type="int", required=true, description="课程id")
     * @ApiParams   (name="lesson_count", type="int", required=true, description="课时")
     * @ApiParams   (name="price", type="int", required=true, description="课程价格")
     * @ApiParams   (name="other_price", type="int", required=true, description="其他价格")
     * @ApiParams   (name="give_lesson", type="int", required=true, description="赠送课时")
     * @ApiParams   (name="remark", type="int", required=true, description="备注")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/11 12:57
     */
    public function add()
    {
        $student_id=$this->request->post('student_id',0);
        $startdate=$this->request->post('startdate');
        $enddate=$this->request->post('enddate',date('Y-m-d',time()+365*24*3600));
        $lesson_id=$this->request->post('lesson_id',0);
        $lesson_count=$this->request->post('lesson_count',0,'floatval');
        $price=$this->request->post('price',0,'floatval');
        $other_price=$this->request->post('other_price',0,'floatval');
        $give_lesson=$this->request->post('give_lesson',0,'floatval');
        $remark=$this->request->post('remark','');
        $rule=[
            'student_id'=>'require','startdate'=>'require|date','enddate'=>'require|date','lesson_id'=>'require','lesson_count'=>'require',
            'price'=>'require','other_price'=>'require','give_lesson'=>'number'
        ];
        $msg=[
            'student_id'=>'学员','startdate'=>'签约日期','enddate'=>'截止日期','lesson_id'=>'课程','lesson_count'=>'课时','other_price'=>'其他价格',
            'give_lesson'=>'赠送课时'
        ];
        $validate=new \think\Validate($rule,[],$msg);
        $data=[
            'creator'=>$this->auth->id,'agency_id'=>$this->auth->agency_id,
            'student_id'=>$student_id,'startdate'=>$startdate,'enddate'=>$enddate,'lesson_id'=>$lesson_id,'lesson_count'=>$lesson_count,
            'price'=>$price,'other_price'=>$other_price,'give_lesson'=>$give_lesson,'remark'=>$remark,'status'=>1
        ];
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            $data['type']=1;
            $data['sno']=\app\common\model\Contract::makeSno();
            $data['total_fee']=$price*$lesson_count+$other_price;
            $data['status']=1;
            $info=\app\common\model\Contract::create($data);
            if ($info){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }
    }


    /**
     * 查询合约列表
     * @ApiMethod   (GET)
     * @ApiParams   (name="page", type="int", required=true, description="当前页")
     * @ApiParams   (name="page_size", type="int", required=true, description="每页总数")
     * @ApiParams   (name="student_id", type="int", required=false, description="学员id")
     * @ApiParams   (name="startdate", type="date", required=false, description="学员id")
     * @ApiParams   (name="enddate", type="date", required=false, description="学员id")
     * @ApiParams   (name="lesson_id", type="int", required=false, description="课程id")
     * @ApiParams   (name="creator", type="int", required=false, description="操作人")
     * @ApiParams   (name="get_myself", type="int", required=false, description="是否获取本人的订单")
     * @ApiReturnParams   (name="type", type="int", required=true, description="类型：参见type_text")
     * @ApiReturnParams   (name="type_text", type="int", required=true, description="类型：1=>'新签',2=>'赠送',3=>'退款'")
     * @ApiReturnParams   (name="sno", type="string", required=true, description="合约编号")
     * @ApiReturnParams   (name="student_id", type="int", required=true, description="学生id")
     * @ApiReturnParams   (name="startdate", type="date", required=true, description="签约日期")
     * @ApiReturnParams   (name="enddate", type="date", required=true, description="截止日期")
     * @ApiReturnParams   (name="lesson_id", type="int", required=true, description="课程ID")
     * @ApiReturnParams   (name="lesson_count", type="float", required=true, description="课时")
     * @ApiReturnParams   (name="price", type="float", required=true, description="价格")
     * @ApiReturnParams   (name="total_fee", type="float", required=true, description="总金额")
     * @ApiReturnParams   (name="remark", type="string", required=true, description="备注")
     * @ApiReturnParams   (name="creator", type="string", required=true, description="创建人")
     * @ApiReturnParams   (name="createtime", type="string", required=true, description="创建时间")
     * @ApiReturnParams   (name="rest_day", type="int", required=true, description="到期天数")
     * @ApiReturnParams   (name="rest_shedule", type="string", required=true, description="剩余课节数")
     * @ApiReturnParams   (name="lesson", type="string", required=true, description="课程")
     * @ApiReturnParams   (name="orgin_data", type="array", required=true, description="修改历史记录")
     * @ApiReturnParams   (name="data：total_fee", type="decimal", required=true, description="合约统计金额，注意是跟data并级")
     * @ApiReturnParams   (name="data：total_lesson", type="decimal", required=true, description="总共课时数，注意是跟data并级")
     * @ApiReturnParams   (name="data：rest_lesson", type="decimal", required=true, description="剩余课时数，注意是跟data并级")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/11 12:59
     */
    public function get_list()
    {
        $page=$this->request->get('page',1);
        $page_size=$this->request->get('page_size',20);
        $student_id=$this->request->get('student_id',0);
        $startdate=$this->request->get('startdate');
        $enddate=$this->request->get('enddate');
        $lesson_id=$this->request->get('lesson_id');
        $creator=$this->request->get('creator');
        $get_myself=$this->request->get('get_myself',0,'intval');
        $map=[];
        $map['agency_id']=$this->auth->agency_id;
        if ($student_id){$map['student_id']=$student_id;}
        if ($startdate){$map['createtime']=['between',[strtotime($startdate),strtotime($enddate)]];}
        if ($lesson_id){$map['lesson_id']=$lesson_id;}
        if ($creator){$map['creator']=$creator;}
        $map['status']=1;
        $map['is_old']=0;//作废标识
        if ($get_myself){
            $student_id=\db('student')->where(['mobile'=>$this->auth->mobile,'status'=>1])->column('id');
            $map['type']=['eq',1];//0=>'默认',1=>'新签',2=>'赠送',3=>'续费',4=>'退款'
            $map['student_id']=['in',$student_id];
        }
        $data=model('ContractRecord')->where($map)->order('id desc')->paginate($page_size,[],['page'=>$page])->jsonSerialize();
        $total_fee=model('ContractRecord')->where($map)->sum('total_fee');
        $lesson_count=model('Contract_record')->where($map)->sum('lesson_count');
        $data['total_fee']=$total_fee;
        $data['total_lesson']=$lesson_count;
        $lesson_finish=db('shedule_finish')->where(['student_id'=>['in',$student_id],'lesson_id'=>$lesson_id])->count();
        $data['rest_lesson']=$lesson_count-$lesson_finish;
        foreach ($data['data'] as &$item){
            $check=model('ContractRecord')->where(['is_old'=>1,'sno'=>$item['sno'],'status'=>1])->order('updatetime desc')->select();
            if ($check){
                $item['orgin_data']=$check;
            }else{
                $item['orgin_data']=[];
            }
        }
        $this->success('操作成功',$data);
    }


    /**
     * 合约续费
     * @ApiMethod   (POST)
     * @ApiParams   (name="student_id", type="int", required=true, description="学员id")
     * @ApiParams   (name="startdate", type="int", required=true, description="签约日期：传YYYY-mm-dd")
     * @ApiParams   (name="enddate", type="int", required=true, description="有效期（传YYYY-mm-dd）")
     * @ApiParams   (name="lesson_id", type="int", required=true, description="课程id")
     * @ApiParams   (name="lesson_count", type="int", required=true, description="课时")
     * @ApiParams   (name="price", type="int", required=true, description="课程价格")
     * @ApiParams   (name="other_price", type="int", required=true, description="其他价格")
     * @ApiParams   (name="give_lesson", type="int", required=true, description="赠送课时")
     * @ApiParams   (name="remark", type="int", required=true, description="备注")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/12 16:18
     */
    public function continue_contract()
    {
        $student_id=$this->request->post('student_id',0);
        $startdate=$this->request->post('startdate');
        $enddate=$this->request->post('enddate',date('Y-m-d',time()+365*24*3600));
        $lesson_id=$this->request->post('lesson_id',0);
        $lesson_count=$this->request->post('lesson_count',0,'floatval');
        $price=$this->request->post('price',0,'floatval');
        $other_price=$this->request->post('other_price',0,'floatval');
        $give_lesson=$this->request->post('give_lesson',0,'floatval');
        $remark=$this->request->post('remark','');
        $rule=[
            'student_id'=>'require','startdate'=>'require|date','enddate'=>'require|date','lesson_id'=>'require','lesson_count'=>'require',
            'price'=>'require','other_price'=>'require','give_lesson'=>'number'
        ];
        $msg=[
            'student_id'=>'学员','startdate'=>'签约日期','enddate'=>'截止日期','lesson_id'=>'课程','lesson_count'=>'课时','other_price'=>'其他价格',
            'give_lesson'=>'赠送课时'
        ];
        $validate=new \think\Validate($rule,[],$msg);
        $data=[
            'creator'=>$this->auth->id,
            'student_id'=>$student_id,'startdate'=>$startdate,'enddate'=>$enddate,'lesson_id'=>$lesson_id,'lesson_count'=>$lesson_count,
            'price'=>$price,'other_price'=>$other_price,'give_lesson'=>$give_lesson,'remark'=>$remark,'status'=>1
        ];
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            $data['sno']=\app\common\model\Contract::makeSno();
            $data['total_fee']=$price*$lesson_count+$other_price;
            $data['status']=1;
            $data['type']=3;
            $data['creator']=$this->auth->id;
            $data['agency_id']=$this->auth->agency_id;
            $info=\app\common\model\Contract::create($data,true);
            if ($info){
                $this->success('续费成功');
            }else{
                $this->error('续费失败');
            }
        }
    }


    /**
     * 合约退费
     * @ApiMethod   (POST)
     * @ApiParams   (name="sno", type="string", required=true, description="合约编号")
     * @ApiParams   (name="lesson_count", type="int", required=true, description="退课时数")
     * @ApiParams   (name="money", type="decimal", required=true, description="退费金额")
     * @ApiParams   (name="remark", type="string", required=true, description="备注")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/12 16:37
     */
    public function refund_contract()
    {
        $sno=$this->request->post('sno');
        $lesson_count=$this->request->post('lesson_count');
        $money=$this->request->post('money');
        $remark=$this->request->post('remark');
        $rule=['sno'=>'require','lesson_count'=>'require|gt:0','money'=>'require|gt:0'];
        $msg=['sno'=>'合约编号','lesson_count'=>'退课时数','money'=>'退费金额','remark'=>'备注'];
        $data=['sno'=>$sno,'lesson_count'=>$lesson_count,'money'=>$money,'remark'=>$remark,'creator'=>$this->auth->id];
        $validate=new \think\Validate($rule,[],$msg);
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }
        $contract=\app\common\model\Contract::get(['sno'=>$sno]);
        if (empty($contract)){
            $this->error('合约不存在');
        }
        if ($money>$contract['total_fee']){
            $this->error('退费金额不能超过合约金额');
        }
        $data['agency_id']=$this->auth->agency_id;
        $info=Refund::create($data);
        if ($info){
            $res=model('contract')->where('sno',$sno)->dec('total_fee',$money)->update(['refund_fee'=>$money]);
            $this->success('退费操作成功');
        }else{
            $this->error('操作失败');
        }
    }


    /**
     * 合约详情
     * @ApiMethod   (GET)
     * @ApiParams   (name="id", type="int", required=true, description="购买记录id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function get_detail()
    {
        $id=$this->request->request('id');
        $data=model('ContractRecord')->where('id',$id)->find();
        if (empty($data)){$data=[];}else{
            $data=$data->toArray();
        }
        $this->success('查询成功',$data);
    }

    /**
     * 修改合约
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="记录id")
     * @ApiParams   (name="sno", type="string", required=true, description="合约编号")
     * @ApiParams   (name="lesson_count", type="int", required=true, description="课时")
     * @ApiParams   (name="price", type="decimal", required=true, description="单价")
     * @ApiParams   (name="other_price", type="decimal", required=true, description="其他金额")
     * @ApiParams   (name="startdate", type="date", required=true, description="开始日期")
     * @ApiParams   (name="enddate", type="date", required=true, description="截止日期")
     * @ApiParams   (name="remark", type="string", required=true, description="备注")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/12 18:13
     */
    public function edit_record()
    {
        $id=$this->request->post('id');
        $sno=$this->request->post('sno');
        $lesson_count=$this->request->post('lesson_count');
        $price=$this->request->post('price');
        $other_price=$this->request->post('other_price');
        $startdate=$this->request->post('startdate');
        $enddate=$this->request->post('enddate');
        $remark=$this->request->post('remark');
        $rule=['id'=>'require|gt:0','sno'=>'require','lesson_count'=>'require|gt:0','price'=>'require','other_price'=>'require','startdate'=>'require','enddate'=>'require'];
        $msg=['id'=>'记录id','sno'=>'合约编号','lesson_count'=>'课时','price'=>'单价','other_price'=>'其他单价'];
        $data=['id'=>$id,'sno'=>$sno,'lesson_count'=>$lesson_count,'price'=>$price,'other_price'=>$other_price,'startdate'=>$startdate,'enddate'=>$enddate,'remark'=>$remark];
        $validate=new \think\Validate($rule,[],$msg);
        $check=$validate->check($data);
        if (!$check){$this->error($validate->getError());}
        $data['total_fee']=$lesson_count*$price+$other_price;
        //写入新纪录
        $contract_record=db('contract_record')->where('id',$id)
            ->field('id,is_old,creator,createtime,updatetime,sno,lesson_count,price,other_price,startdate,enddate,remark',true)->find();
        $data=array_merge($data,$contract_record);
        unset($data['id']);
        $data['creator']=$this->auth->id;
        Db::startTrans();
        if(ContractRecord::update(['is_old'=>1],['id'=>$id],true)){
            $info=ContractRecord::create($data,true);
            if ($info){
                //更新contract
                $last_record=\app\common\model\Contract::get(['sno'=>$sno]);
                $dis_total=$price*$lesson_count+$other_price-$last_record['total_fee'];

                if(\app\common\model\Contract::update($data,['sno'=>$sno],true)){
                    \app\common\model\Finance::create(['contract_money'=>$dis_total,'date'=>date('Y-m-d',time())],true);
                }

                Db::commit();
                $this->success('修改成功');
            }else{
                Db::rollback();
                $this->error('修改失败');
            }
        }else{
            Db::rollback();
            $this->error('操作失败');
        }
    }


    /**
     * 删除合约记录
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="记录id")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/12 19:00
     */
    public function delete_contract_record()
    {
        $id=$this->request->post('id',0,'intval');
        if (empty($id)){
            $this->error('参数错误');
        }
        $check=ContractRecord::get($id);
        if (empty($check)){
            $this->error('查无数据');
        }
        if ($check['agency_id']!=$this->auth->agency_id){
            $this->error('无权限删除其他机构合约');
        }
        $info=ContractRecord::update(['status'=>0],['id'=>$id]);
        if ($info){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}