<?php
namespace app\lib\exception;

use Exception;

class ParameterException extends BaseException
{
    protected $code = 400;
    protected $msg = '参数错误';
    protected $errorCode = 10000;

}