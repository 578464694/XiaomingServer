<?php
namespace app\lib\exception;

use think\exception\Handle;
use think\Log;
use think\Request;

class ExceptionHandler extends Handle
{
    private $code = 400;
    private $msg = '';
    private $errorCode = 10000;

    public function render(\Exception $e)
    {
        if ($e instanceof BaseException) {
            // 如果是自定义异常
            $this->code = $e->getHttpCode();
            $this->msg = $e->getMsg();
            $this->errorCode = $e->getErrorCode();
        }
        else
        {
            if (config('app_debug'))
            {
                return parent::render($e);
            }
            else
            {
                $this->code = 500;
                $this->msg = '服务器内部错误，我不想告诉你'.$e->getMessage();
                $this->error_code = 999;
                $this->recordErrorLog($e);
            }
        }
        $request = Request::instance();
        $result['request_url'] = $request->url();

        $result = [
            'code' => $this->code,
            'msg' => $this->msg,
            'error_code' => $this->errorCode,
            'request_url' => $request->url()
        ];
        return json($result, $this->code);
    }

    private function recordErrorLog(\Exception $e)
    {
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH,
            'level' => ['error']
        ]);
        Log::record($e->getMessage(), 'error');
    }
}