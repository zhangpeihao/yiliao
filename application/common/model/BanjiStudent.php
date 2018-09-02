<?php
/**
 * @desc :Created by PhpStorm.
 * @author : yunhe <2846359640@qq.com>
 * @since: 2018/4/4 17:24
 */
namespace app\common\model;

use think\Model;

class BanjiStudent extends Model{
    protected $name='banji_student';

    protected $autoWriteTimestamp='int';

    protected $createTime='createtime';

    protected $updateTime='updatetime';

    protected $dateFormat='Y-m-d H:i';

    protected $append=[
        'student'
    ];

    public function getTypeList()
    {
        return [1=>'普通',2=>'插班'];
    }

    public function getStudentAttr($value,$data)
    {
        $value=$value?$value:$data['student_id'];
        return (string)db('Student')->cache(60)->where('id',$value)->value('username');
    }

    public function getStudentBanji($student_id)
    {
        $banji=$this->where('student_id',$student_id)->where('status',1)->distinct(true)->column('banji_id');
        if (empty($banji)){
            $data=[];
        }else{
            $data=db('Banji')->where('id','in',$banji)->field('id,name')->select();
            if (empty($data)){
                return [];
            }
        }
        return $data;
    }

    public static function is_full($banji_id)
    {
        $banji=Banji::get($banji_id);
        $count=BanjiStudent::where(['banji_id'=>$banji_id,'status'=>1])->group('student_id')->count();
        if ($banji['max_member']>=$count){
            return 0;
        }else{
            return 1;
        }
    }
}