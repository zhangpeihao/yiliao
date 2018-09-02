<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/12 16:26
 */
namespace app\common\model;

use think\Model;

class Finance extends Model{
    protected $name='finance';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';
    
    public function setDateAttr($value,$data)
    {
        if ($data['date']){
            return $data['date'];
        }else{
            return date('Y-m-d',time());
        }
    }
}