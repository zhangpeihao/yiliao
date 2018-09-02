<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/18 15:17
 */
namespace app\common\model;

use think\Model;

class AgencyMember extends Model{
    protected $name='agency_member';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=['type_text','agency_info'];

    public function getTypeList($key)
    {
        $list=[1=>'机构所有者',2=>'教务',3=>'老师',0=>'未知',4=>'会员'];
        if ($key){
            return $list[$key];
        }else{
            return $list;
        }
    }

    public function getTypeTextAttr($value,$data)
    {
        if (empty($data['type'])){$data['type']=0;}
        return $this->getTypeList($data['type']);
    }

    public function getAgencyInfoAttr($value,$data)
    {
        $res=Agency::get($data['agency_id']);
        if ($res){
            return $res->toArray();
        }else{
            return (object)[];
        }
    }
}