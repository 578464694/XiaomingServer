<?php

namespace app\lib\exception;


class StockException extends BaseException
{
    protected $code = 400;
    protected $msg = '库存量不足';
    protected $errorCode = 20001;
}