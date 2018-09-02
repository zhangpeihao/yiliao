<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace app\common\model;
use fast\Random;
use think\Db;
use think\Model;
/**
 * 话题模型
 * @author jry <598821125@qq.com>
 */
class ThirdUser extends Model{
    protected $name='third_user';
    protected $autoWriteTimestamp='int';
    protected $createTime='ctime';
    protected $updateTime='utime';

}
