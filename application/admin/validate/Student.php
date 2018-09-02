<?php

namespace app\admin\validate;

use think\Validate;

class Student extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'username'=>'require',
        'agency_id'=>'require|gt:0',
        'gender'=>'require',
        'mobile'=>'require',
        'birthday'=>'require'
    ];
    /**
     * 提示消息
     */
    protected $message = [
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['username','agency_id','gender','mobile','birthday'],
        'edit' => [],
    ];

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        $this->field=[
            'username'=>__('Username'),
            'agency_id'=>__('Agency_id'),
            'gender'   =>__('Gender'),
            'mobile'   =>__('Mobile'),
            'birthday'=>__('Birthday')
        ];
        parent::__construct($rules, $message, $field);
    }
}
