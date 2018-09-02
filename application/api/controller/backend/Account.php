<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/19
 * Time: 11:38
 */
namespace app\api\controller\backend;

use app\common\controller\Api;
use app\common\model\MallAccountLog;
use app\common\model\Teacher;
use think\Db;

/**
 * 教务端记账系统
 * Class Account
 * @package app\api\controller\backend
 */
class Account extends Api
{

    protected $noNeedLogin='';

    protected $noNeedRight='*';

    /**
     * 查询账务记录
     * @ApiMethod   (GET)
     * @ApiParams   (name="year", type="int", required=true, description="年")
     * @ApiParams   (name="month", type="int", required=true, description="月")
     * @ApiParams   (name="day", type="int", required=true, description="日")
     * @ApiParams   (name="type", type="int", required=true, description="类型：1=>'投资',2=>'支出',3=>'收入")
     * @ApiParams   (name="page", type="int", required=true, description="分页")
     * @ApiParams   (name="page_size", type="int", required=true, description="分页大小")
     * @ApiReturnParams   (name="remark", type="string", required=true, description="记账备注")
     * @ApiReturnParams   (name="creator_info", type="array", required=true, description="操作人信息")
     * @ApiReturnParams   (name="date", type="date", required=true, description="日期")
     * @ApiReturnParams   (name="type_text", type="string", required=true, description="类型描述")
     * @ApiReturnParams   (name="ctime", type="string", required=true, description="操作时间")
     * @ApiReturnParams   (name="status", type="int", required=true, description="状态：1成功，0失败")
     * @ApiReturnParams   (name="money", type="float", required=true, description="金额")
     * @ApiReturnParams   (name="pay_sn", type="string", required=true, description="流水号")
     * @ApiReturnParams   (name="order_info", type="array", required=true, description="订单详情：判断是否为空，为空时表示无订单关联")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/19 12:10
     */
    public function get_result()
    {
        $year=request()->get('year',date('Y'),'strval');
        $month=request()->get('month',date('m'),'strval');
        $day=request()->get('day',date('d'),'strval');
        $type=request()->get('type',1,'intval');
        $page=request()->get('page',1,'intval');
        $page_size=request()->get('page_size',20,'intval');
        $map=[];
        if ($year){$map['year']=$year;}
        if ($month){$map['month']=$month;}
        if ($day){$map['day']=$day;}
        $map['type']=$type;
        $map['status']=1;
        $map['agency_id']=$this->auth->agency_id;
        $total=model('Account')->where($map)->sum('money');
        $data=model('Account')->where($map)->order('date desc')->paginate($page_size,'',['page'=>$page])->jsonSerialize();
        $data['total_money']=$total;
        $this->success('查询成功',$data);
    }

    /**
     * 添加记账
     * @ApiMethod   (POST)
     * @ApiParams   (name="title", type="string", required=true, description="款项名称")
     * @ApiParams   (name="money", type="float", required=true, description="金额")
     * @ApiParams   (name="type", type="int", required=true, description="类型：1=>'投资',2=>'支出',3=>'收入")
     * @ApiParams   (name="date", type="date", required=true, description="日期：YYYY-mm-dd")
     * @ApiParams   (name="remark", type="string", required=true, description="备注")
     * @ApiReturnParams   (name="", type="int", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/19 12:12
     */
    public function addAccount(){
        $title=request()->post('title','','strval');
        $money=request()->post('money',0,'floatval');
        $type=request()->post('type',0,'intval');//1.欠款=>投入   2.支出  3收入
        $date=request()->post('date','','strval');
        $remark=request()->post('remark','','strval');

        if (!$title||!$money || !$type || !$date){
            $this->error('请补全信息');
        }
        $check=Teacher::get(['mobile'=>$this->auth->mobile,'status'=>1]);
        if (empty($check)){
            $this->error('没有教务权限');
        }
        if (empty($date)){$date=date('Y-m-d',time());}
        $info=\app\common\model\Account::addAccount($this->auth->id,$money,$type,$date,'',$this->auth->id,$remark,$title);
        if ($info){
            $this->success('入账成功');
        }else{
            $this->error('入账失败');
        }
    }

    /**
     * 修改账务记录
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="数据id")
     * @ApiParams   (name="title", type="string", required=true, description="款项名称")
     * @ApiParams   (name="money", type="float", required=true, description="金额")
     * @ApiParams   (name="type", type="int", required=true, description="类型：1=>'投资',2=>'支出',3=>'收入")
     * @ApiParams   (name="date", type="date", required=true, description="日期：YYYY-mm-dd")
     * @ApiParams   (name="remark", type="string", required=true, description="备注")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/19 16:00
     */
    public function edit()
    {
        $id=request()->post('id',0,'intval');
        $title=request()->post('title','','strval');
        $money=request()->post('money',0,'floatval');
        $type=request()->post('type',0,'intval');//1.欠款=>投入   2.支出  3收入
        $date=request()->post('date','','strval');
        $remark=request()->post('remark','','strval');

        if (!$id||!$title||!$money || !$type || !$date){
            $this->error('请补全信息');
        }
        $check=Teacher::get(['mobile'=>$this->auth->mobile,'status'=>1]);
        if (empty($check)){
            $this->error('没有教务权限');
        }
        if (empty($date)){$date=date('Y-m-d',time());}

        $account_info=\app\common\model\Account::get(['id'=>$id]);
        $info=\app\common\model\Account::updateAccount(
            $account_info['uid'],
            $money,
            $type,
            $date,
            $account_info['pay_sn'],
            $this->auth->id,
            $remark,
            $title,
            $id);
        if($info){
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }


    /**
     * 移除记账日志
     * @ApiMethod   (POST)
     * @ApiParams   (name="id", type="int", required=true, description="记录id")
     * @ApiReturnParams   (name="", type="string", required=true, description="")
     * @ApiReturn (data="")
     * @author : yunhe <2846359640@qq.com>
     * @date: 2018/7/19 16:09
     */
    public function remove_record()
    {
        $id=request()->post('id',0,'intval');
        $info=\app\common\model\Account::update(['status'=>0],['id'=>$id]);
        if ($info){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
}