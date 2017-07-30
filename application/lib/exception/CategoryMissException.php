<?php
namespace app\lib\exception;

class CategoryMissException extends BaseException
{
    protected $code = 404;
    protected $msg = '请求的分类不存在';
    protected $errorCode = 50000;

}