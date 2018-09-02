<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 22:17
 */
namespace app\common\model;

use think\Model;

class ContractStudent extends Model{
    protected $name='contract_student';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';


    protected $append=[
        'status_text'
    ];

    public function getStatusTextAttr($value,$data)
    {
        $status=[0=>'禁用',1=>'正常',2=>'过期'];
        return $status[$data['status']];
    }
}