<?php
namespace app\lib\exception;

class ProductMissException extends BaseException
{
    protected $errorCode = 404;
    protected $msg = '你请求的product不存在';
    protected $code = 999;
}