<?php

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
        'code' => 'require|notEmpty'    //必填，非空
    ];

    protected $message = [
        'code.require' => 'code为必填项',
        'code.notEmpty' => 'code不能为空'
    ];
}