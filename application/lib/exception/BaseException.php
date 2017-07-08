<?php

namespace app\lib\exception;

use think\Exception;

class BaseException extends Exception
{
    protected $code = 400;
    protected $msg = '参数错误';
    protected $errorCode = 10000;

    public function __construct($params = [])
    {
        // 过滤非数组
        if(!is_array($params)) {
            return ; // 返回空，意思是不强制要求参数为数组,认为需要保持原来的成员状态
//            throw new Exception('参数必须是数组'); // 强制要求参数为数组
        }
        if(array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }
        if(array_key_exists('msg', $params)) {
            $this->msg = $params['msg'];
        }
        if(array_key_exists('errorCode', $params)) {
            $this->errorCode = $params['errorCode'];
        }
    }

    /**
     * 获得错误状态码
     * @return int
     */
    public function getHttpCode() {
        return $this->code;
    }

    public function setMsg($msg) {
        $this->msg = $msg;
    }

    /**
     * 获得错误概述
     * @return string
     */
    public function getMsg() {
        return $this->msg;
    }

    /**
     * 获得错误码
     * @return int
     */
    public function getErrorCode() {
        return $this->errorCode;
    }

    /**
     * 设置错误信息
     * @code    HTTP 状态码
     * @msg     错误概述
     * @errorCode 错误码
     */
    public function setErrorInfo($code, $msg, $errorCode) {
        $this->code = $code;
        $this->msg = $msg;
        $this->errorCode = $errorCode;
    }

    /**
     * 返回错误信息
     * @return array
     */
    public function getErrorInfo() {
        $error[] = [
            'code' => $this->code,
            'msg' => $this->msg,
            'error_code' => $this->errorCode
        ];
        return $error;
    }

}