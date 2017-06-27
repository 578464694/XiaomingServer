<?php
namespace app\lib\exception;

use Exception;
use think\exception\Handle;

class ExceptionHandler extends Handle
{
    public function render(Exception $e)
    {
        if($e instanceof BaseException) {
            return $e->getErrorInfo();
        }
        return 1;
    }
}