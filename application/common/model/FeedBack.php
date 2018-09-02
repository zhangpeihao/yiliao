<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 13:29
 */
namespace app\common\model;

use think\Model;

class FeedBack extends Model{
    protected $name='feed_back';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';
}