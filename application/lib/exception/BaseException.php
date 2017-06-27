<?php

namespace app\lib\exception;


class BaseException
{
    protected $code = 400;
    protected $msg = '参数错误';
    protected $errorCode = 10000;

    /**
     * 获得错误状态码
     * @return int
     */
    public function getCode() {
        return $this->code;
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
//    public function getErrorInfo() {
//        $error[] = [
//            'code' => $this->code,
//            'msg' => $this->msg,
//            'errorCode' => $this->errorCode
//        ];
//        return $error;
//    }

}