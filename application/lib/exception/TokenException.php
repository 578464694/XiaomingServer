<?php

namespace app\lib\exception;


class TokenException extends BaseException
{
    protected $code = 401;
    protected $msg = 'Token已过期或 Token无效';
    protected $errorCode = 10001;
}