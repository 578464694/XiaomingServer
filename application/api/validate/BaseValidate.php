<?php
namespace app\api\validate;

use app\lib\exception\ParameterException;
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
        $result = $this->batch()->check($params); // 批量验证

        if(!$result) {
            $e = new ParameterException([
                'msg' => $this->error
            ]);
            throw $e;
        } else {
            return true;
        }
    }
}