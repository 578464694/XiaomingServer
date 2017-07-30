<?php
namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    protected $code = 401;
    protected $msg = '权限不足';
    protected $errorCode = 10001;
}