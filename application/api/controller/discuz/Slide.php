<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/12
 * Time: 16:10
 */
namespace app\api\controller\discuz;

use app\common\controller\Api;

/**
 * 教务端社区首页轮播
 * Class Slide
 * @package app\api\controller\discuz
 */
class Slide extends Api
{
    protected $noNeedRight='*';

    protected $noNeedLogin='*';

     /**
      * 获取帖子评论列表
      * @ApiMethod   (POST)
      * @ApiParams   (name="", type="int", required=true, description="")
      * @ApiReturnParams   (name="image", type="url", required=true, description="轮播图url")
      * @ApiReturnParams   (name="link_id", type="id", required=true, description="跳转的帖子ID")
      */
    public function get_list()
    {
        $data=model('DisSlide')->where('status',1)->order(['sort'=>'asc','id'=>'desc'])->select();
        $this->success('查询成功',$data);
    }
}