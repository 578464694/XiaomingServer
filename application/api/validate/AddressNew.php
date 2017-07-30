<?php
namespace app\api\validate;


class AddressNew extends BaseValidate
{
    protected $rule = [
        'name' => 'require|notEmpty',
        'mobile' => 'require|notEmpty|isMobile',
        'province' => 'require|notEmpty',
        'city' => 'require|notEmpty',
        'country' => 'require|notEmpty',
        'detail' => 'require|notEmpty'
    ];

    protected $message = [
        'mobile.isMobile' => '手机号格式不正确'
    ];
}