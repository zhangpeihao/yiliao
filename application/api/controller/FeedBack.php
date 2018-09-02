<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 13:22
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 意见反馈
 * Class FeedBack
 * @package app\api\controller
 */
class FeedBack extends Api{

    protected $noNeedRight='*';
    /**
     * 意见反馈
     * @ApiMethod   (POST)
     * @ApiParams   (name="from", type="int", required=true, description="来源：1教务端，2家长端")
     * @ApiParams   (name="contact", type="string", required=true, description="联系方式")
     * @ApiParams   (name="content", type="string", required=true, description="意见内容")
     * @ApiReturn (data="{}")
     * @author : yunhe <2846359640@qq.com>
     * @date: author
     */
    public function add()
    {
        $content=$this->request->post('content');
        $contact=$this->request->post('contact');
        $from=$this->request->post('from',1);
        $uid=$this->auth->id;
        $rule=['content'=>'require','contact'=>'require'];
        $validate=new \think\Validate($rule,[],['content'=>'意见内容','contact'=>'联系方式']);
        $data=[
            'content'=>$content,'contact'=>$contact,'from'=>$from,'user_id'=>$uid,'status'=>1
        ];
        $check=$validate->check($data);
        if (!$check){
            $this->error($validate->getError());
        }else{
            $data['agency_id']=$this->auth->agency_id;
            $info=\app\common\model\FeedBack::create($data,true);
            if ($info){
                $this->success('提交成功');
            }else{
                $this->error('提交失败');
            }
        }
    }
}