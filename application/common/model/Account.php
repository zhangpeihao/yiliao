<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/5/25
 * Time: 17:04
 */
namespace app\common\model;

use app\common\library\Auth;
use think\Db;
use think\Model;

class Account extends Model {
    protected $name='mall_account';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    protected $append=['type_text','creator_info','order_info'];

    protected $dateFormat='Y-m-d H:i';

    protected static function init()
    {
        Account::event('before_insert',function ($account){
           $auth=Auth::instance();
           $account->agency_id=$auth->agency_id;
        });
    }

    public function getOrderInfoAttr($value,$data)
    {
        if ($data['pay_sn']){
            $order_info=\model('MallProductOrder')->where('out_trade_no',$data['pay_sn'])->find();
            if ($order_info){
                return $order_info->toArray();
            }else{
                return (object)[];
            }
        }else{
            return (object)[];
        }
    }

    public function setDateAttr($value,$data)
    {
        if (!strstr($value,'-')){
            return $value;
        }else{
            return strtotime($value);
        }
    }

    public function getTypeTextAttr($value,$data)
    {
        $list=[1=>'投资',2=>'支出',3=>'收入'];
        return $list[$data['type']];
    }

    public function getDateAttr($value,$data)
    {
        return date('Y-m-d',$value);
    }


    public function getCreatorInfoAttr($value,$data)
    {
        if (empty($data['creator'])){return (object)[];}
        $res=\model('User')->where('id',$data['creator'])->field('id,avatar,username,mobile')->find();
        if (empty($res)){
            return (object)[];
        }else{
            return $res->toArray();
        }
    }
    /**
     *
     * @param $uid
     * @param $money
     * @param int $type
     * @param $date
     * @param string $paysn
     * @param int $creator
     * @param string $remark
     * @return bool
     */
    public static function addAccount($uid,$money,$type=3,$date,$paysn='',$creator=0,$remark="",$title,$student_id=0){
        if (!$money || !$type || !$date){
            return false;
        }
        $tmp_date=$date;
        if (!strstr($date,'-')){
            $date=date('Y-m-d',$date);
        }
        list($year,$month,$day)=explode('-',$date);
        $data=[
            'uid'=>$uid,
            'student_id'=>$student_id,
            'creator'=>$creator,
            'money'=>$money,
            'type'=>$type,
            'year'=>$year,
            'month'=>$month,
            'day'=>$day,
            'date'=>$tmp_date,
            'remark'=>$remark,
            'pay_sn'=>$paysn,
            'status'=>1,
            'title'=>$title
        ];
        $info=Account::create($data,true);
        if ($info){
            return true;
        }else{
            return false;
        }
    }
    /**
     *
     * @param $uid
     * @param $money
     * @param int $type
     * @param $date
     * @param string $paysn
     * @param int $creator
     * @param string $remark
     * @return bool
     */
    public static function updateAccount($uid,$money,$type=3,$date,$paysn='',$creator=0,$remark="",$title,$account_id=0){
        if ($uid<=0){
            return false;
        }
        if (!$money || !$type || !$date){
            return false;
        }
        $tmp_date=$date;
        if (!strstr($date,'-')){
            $date=date('Y-m-d',$date);
        }
        list($year,$month,$day)=explode('-',$date);
        $data=[
            'uid'=>$uid,
            'updator'=>$creator,
            'money'=>$money,
            'type'=>$type,
            'year'=>$year,
            'month'=>$month,
            'day'=>$day,
            'date'=>$tmp_date,
            'remark'=>$remark,
            'pay_sn'=>$paysn,
            'status'=>1,
            'title'=>$title
        ];
        $info=0;
        if ($account_id){
            $account_info=\app\common\model\Account::get(['id'=>$account_id]);
            $info=Account::update($data,['id'=>$account_id]);
        }elseif($paysn){
            $account_info=\app\common\model\Account::get(['pay_sn'=>$paysn]);
            $info=Account::update($data,['pay_sn'=>$paysn]);
        }
        if ($info){
            //添加修改记录
            $table_info=Db::table('information_schema.columns')->where('table_name','fa_mall_account')->column('COLUMN_COMMENT','COLUMN_NAME');
            $count=0;
            $log_id=md5($creator.'-'.$account_id.'-'.time());
            foreach ($data as $k=>$v){
                if ($v!=$account_info[$k]){
                    $count++;
                    $info=MallAccountLog::create([
                        'log_id'=>$log_id,
                        'account_id'=>$account_info['id'],
                        'field'=>$k,
                        'last_value'=>$account_info[$k],
                        'modify_value'=>$v,
                        'creator'=>$creator,
                        'remark'=>$table_info[$k]
                    ]);
                }
            }
            return true;
        }else{
            return false;
        }
    }
}