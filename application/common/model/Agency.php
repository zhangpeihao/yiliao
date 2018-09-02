<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/18 15:01
 */
namespace app\common\model;

use think\Model;

class Agency extends Model{
    protected $name='agency';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    public static function init()
    {
        Agency::event('after_insert',function ($agency){
            AgencyMember::create([
                'type'=>1,'agency_id'=>$agency['id'],'uid'=>$agency['creator'],'status'=>1,'creator'=>$agency['creator']
            ]);
            //自动添加会员等级
            UserLevel::create([
                'agency_id'=>$agency['id'],'level'=>0,'levelname'=>'新手','discount'=>0,'creator'=>$agency['creator']
            ]);
        });
        Agency::event('before_insert',function ($agency){
            $check=db('agency')->max('sno');
            if (empty($check)){$check=100000;}
            $agency->sno=$check+1;
        });
    }

    public function get_my_join($uid)
    {
        $list=model('AgencyMember')->where('uid',$uid)->select();
        return $list;
    }
}