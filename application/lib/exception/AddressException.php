<?php
namespace app\lib\exception;


class AddressException extends BaseException
{
    protected $code = 403;
    protected $msg = '数据添加失败';
    protected $errorCode = 70000;
}