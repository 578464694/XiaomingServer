<?php
namespace app\lib\exception;

class BannerMissException extends BaseException
{
    protected $code = 404;
    protected $msg = '请求的Banner不存在';
    protected $errorCode = 40000;

}