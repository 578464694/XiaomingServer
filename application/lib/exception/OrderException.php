<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/25
 * Time: 23:18
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    protected $code = 404;
    protected $msg = '订单不存在，请检查ID';
    protected $errorCode = 80001;
}