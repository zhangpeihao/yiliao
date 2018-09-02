<?php

//配置文件
return [
    'exception_handle'        => '\\app\\api\\library\\ExceptionHandle',
    'wechat_mini'=>[
        'app_id' => 'wx92b920e9784ec743',
        'secret' => 'c8ac021ce436d305bff4217ce226f8aa',

        // 下面为可选项
        // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
        'response_type' => 'array',

        'log' => [
            'level' => 'debug',
            'file' => './runtime/log/wechat.log',
        ],

    ]
];
