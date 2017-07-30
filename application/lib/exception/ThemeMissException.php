<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/9
 * Time: 17:44
 */

namespace app\lib\exception;


class ThemeMissException extends BaseException
{
    protected $code = 404;
    protected $msg = '请求主题不存在';
    protected $errorCode = 30000;
}