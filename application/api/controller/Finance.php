<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 12:15
 */
namespace app\api\controller;

use app\common\controller\Api;
use think\helper\Time;

/**
 * 财务
 * Class Finance
 * @package app\api\controller
 */
class Finance extends Api{

    protected $noNeedLogin='*';
    protected $noNeedRight='*';

    /**
     * 获取本月财务记录
     * @ApiMethod   (POST)
     * @ApiParams   (name="month", type="month", required=true, description="月份，YYYY-mm")
     * @ApiReturnParams   (name="month", type="string", required=true, description="查询月份")
     * @ApiReturnParams   (name="sum_continue", type="decimal", required=true, description="续费金额")
     * @ApiReturnParams   (name="sum_contract", type="decimal", required=true, description="合约金额")
     * @ApiReturnParams   (name="sum_refund", type="decimal", required=true, description="退款金额")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1523527578','data':{'month':'2018-04','sum_contract':240,'sum_continue':0,'sum_refund':0}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/12 17:33
     */
    public function get_data()
    {
        $month=$this->request->request('month');
        if (empty($month)){
            $month=date('Y-m',time());
        }
        $time=strtotime($month.'-01 00:00:00');
        $stardate=date('Y-m-d',$time);
        $enddate=date('Y-m-t',$time);
        $map=[];
        $map['date']=['between',[$stardate,$enddate]];
        $map['agency_id']=$this->auth->agency_id;
        $data=db('finance')->where($map)->field('sum(contract_money) as sum_contract,sum(continue_money) as sum_continue,sum(refund_money) as sum_refund')->select();
        $sum_contract=floatval($data[0]['sum_contract']);
        $sum_continue=floatval($data[0]['sum_continue']);
        $sum_refund=floatval($data[0]['sum_refund']);
        $this->success('查询成功',[
            'month'=>$month,'sum_contract'=>$sum_contract,'sum_continue'=>$sum_continue,'sum_refund'=>$sum_refund
        ]);
    }


    /**
     * 获取课程收入统计
     * @ApiMethod   (POST)
     * @ApiParams   (name="month", type="month", required=true, description="月份，YYYY-mm")
     * @ApiReturn (data="{'code':1,'msg':'查询成功','time':'1524118837','data':{'total_fee':754,'other_price':74,'lesson':[{'lesson_id':10,'lesson':'三弦乐','lesson_fee':20,'fee_rate':'2.65%'},{'lesson_id':12,'lesson':'初级班','lesson_fee':200,'fee_rate':'26.53%'},{'lesson_id':2,'lesson':'爵士鼓初级16课','lesson_fee':120,'fee_rate':'15.92%'},{'lesson_id':3,'lesson':'民谣弹唱初级16课','lesson_fee':414,'fee_rate':'54.91%'}]}}")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/4/19 14:04
     */
    public function get_lesson_finance()
    {
        $month=$this->request->request('month',date('Y-m',\time()));
        if (empty($month)){
            $month=date('Y-m',time());
        }
        $time=strtotime($month.'-01 00:00:00');
        $stardate=date('Y-m-d',$time);
        $enddate=date('Y-m-t',$time);
        $map=[];
        $map['createtime']=['between',[strtotime($stardate),strtotime($enddate)]];
        if ($this->auth->agency_id<=0){
            $this->error('请指定机构');
        }
        $map['agency_id']=$this->auth->agency_id;
        $data=model('ContractRecord')->where($map)->where('status',1)->order('id desc')->select();
        $return_data=[];
        if (!empty($data)){
            $total_fee=array_sum(array_column($data,'total_fee'));
            $other_price=array_sum(array_column($data,'other_price'));
            $lesson=array_unique(array_column($data,'lesson_id'));
            foreach ($lesson as $v){
                $lesson_fee=db('contract_record')->where($map)->where('lesson_id',$v)->sum('total_fee');
                $lesson_data[]=[
                    'lesson_id'=>$v,
                    'lesson'=>db('lesson')->where('id',$v)->value('name'),
                    'lesson_fee'=>$lesson_fee,
                    'fee_rate'=>(round($lesson_fee/$total_fee,4)*100).'%',
                ];
            }
        }else{
            $total_fee=0;
            $other_price=0;
            $lesson_data=[];
        }
        $return_data=[
            'total_fee'=>$total_fee,
            'other_price'=>$other_price,
            'lesson'=>$lesson_data
        ];
        $this->success('查询成功',$return_data);
    }
}