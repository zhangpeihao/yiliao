<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/8 16:57
 */
namespace app\common\model;

use think\Model;

class SheduleLog extends Model{
    protected $name='shedule_log';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=[
        'type_text'
    ];

    protected function getTypeTextAttr($value,$data){
        $list=[1=>'取消结课',2=>'结课'];
    }

    public function record_log($shedule_id,$creator,$username,$type)
    {
        $ip=request()->ip(1);

        
    }
}