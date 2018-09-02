<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/6/21
 * Time: 15:28
 */
namespace app\api\model;

use think\Model;

class ThirdUser extends Model
{
    protected $name='third_user';

    protected $autoWriteTimestamp='int';

    protected $createTime='ctime';

    protected $updateTime='utime';

    /**
     * 检查同一分组下是否有相同的字段
     * @author jry <598821125@qq.com>
     */
    public static function checkExit($open_id){
        $result=array();
        $map['open_id'] =array('eq',$open_id);
        $result = db('third_user')->where($map)->find();
        if (empty($result)){
            return false;
        }else{
            return true;
        }
    }
}