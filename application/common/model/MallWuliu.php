<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/6
 * Time: 17:32
 */
namespace app\common\model;

use think\Model;

class MallWuliu extends Model
{
    protected $name='mall_wuliu';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    protected $dateFormat='Y-m-d H:i';


    protected $append=['wuliu_name'];

    public function getWuliuNameAttr($value,$data)
    {
        return db('mall_wuliu_company')->where('code',$data['post_type'])->value('name');
    }

}