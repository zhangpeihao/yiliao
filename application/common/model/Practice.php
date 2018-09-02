<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/26 15:57
 */
namespace app\common\model;

use app\common\library\Auth;
use think\Exception;
use think\Model;

class Practice extends Model{
    protected $name='practice';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=['status_text','creator'];

    protected static function init()
    {
        Practice::event('before_insert',function ($practice){
            $auth=Auth::instance();
            $practice->agency_id=$auth->agency_id;
        });
    }

    public function setCoverAttr($value,$data)
    {
        $cover="";
        if (empty($cover)){
            $video=strtr($data['video'],[config('img_domain')=>'','http://voyage.oss-cn-beijing.aliyuncs.com'=>'']);
            try{
                $cover=create_video_thumb($video,10);
            }catch (Exception $e){}
        }
        return $cover;
    }

    public function getStatus($status='')
    {
        $list=[0=>'已结束',1=>'未开始',2=>'正在进行'];
        if (isset($status)){
            return $list[$status];
        }else{
            return $list;
        }
    }

    public function getStatusTextAttr($value,$data)
    {
        return $this->getStatus($data['status']);
    }

    public function getCreatorAttr($value,$data)
    {
        if ($data['creator']){
            return (array)model('user')->where('id',$data['creator'])->field('id,username,avatar,score')
                ->find()->toArray();
        }else{
            return [];
        }
    }
}