<?php
namespace app\api\validate;

use think\Exception;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    public function goCheck() {
        // 获取 http 请求参数
        // 进行校验
        // 结果判断
        $request = Request::instance();
        $params = $request->param();
        $result = $this->check($params);

        if(!$result) {
            $error = $this->error;
            throw new Exception($error);
        } else {
            return true;
        }
    }
}