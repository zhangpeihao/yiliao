<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/6
 * Time: 16:49
 */
namespace app\common\model;

use app\common\library\Auth;
use think\Db;
use think\Model;

class MallProductLog extends Model
{
    protected $name='mall_product_log';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    protected $append=['creator_info','log_list'];

    protected $dateFormat='Y-m-d H:i';


    public function getLogListAttr($value,$data)
    {
        if (empty($data['log_id'])){
            return [];
        }else{
            return db('mall_product_log')->field('log_id',true)->where('log_id',$data['log_id'])->order('id desc')->select();
        }
    }

    public function getCreatorInfoAttr($value,$data)
    {
        $res=\model('User')->field('id,username,mobile,avatar,gender')->find($data['creator']);
        if ($res){
            return $res->toArray();
        }else{
            return (object)[];
        }
    }

    public static function add_modify_log($data,$last_info=[])
    {
        $auth=Auth::instance();
        $creator=$auth->id;
        //添加修改记录
        $table_info=Db::table('information_schema.columns')->where('table_name','fa_mall_product')->column('COLUMN_COMMENT','COLUMN_NAME');
        $count=0;
        $log_id=md5($creator.'-'.time());
        foreach ($data as $k=>$v){
            if ($v!=$last_info[$k]){
                $count++;
                $info=MallProductLog::create([
                    'log_id'=>$log_id,
                    'product_id'=>$last_info['id'],
                    'field'=>$k,
                    'last_value'=>$last_info[$k],
                    'modify_value'=>$v,
                    'creator'=>$creator,
                    'remark'=>$table_info[$k]
                ]);
            }
        }
        return true;

    }
}