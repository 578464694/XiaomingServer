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

    /**
     * 正整数校验
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     */
    protected function isPositiveInteger($value, $rule = '', $data = '', $field = '') {
        if(is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }else {
            return false;
        }
    }

    /**
     * 非空验证
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool
     */
    protected function notEmpty($value, $rule = '', $data = '', $field = '') {
        if(empty($value))
        {
            return false;
        }
        return true;
    }

    /**
     * 根据 rule 获得用户输入的参数
     * @return array
     */
    public function getDatasByRule($params)
    {
//        $params = Request::instance()->param();
        if(array_key_exists('uid',$params) || array_key_exists('user_id', $params)) { // 禁止从客户端传入 uid 和 user_id
            throw new ParameterException(
                [
                    'msg' => '参数不合法'
                ]);
        }
        $datas = [];
        foreach ($this->rule as $key => $value)
        {
//            if(array_key_exists($key, $params)) {
//                $datas[$key] = $params[$key];
//            }
            $datas[$key] = $params[$key];
        }
        return $datas;
    }

    /**
     * 匹配手机号
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool
     */
    public function isMobile($value, $rule = '', $data = '', $field = '') {
        $regex = '^1(3|5|4|7|8)[0-9]\d{8}$^';
        if(preg_match($regex,$value)) {
            return true;
        } else {
            return false;
        }
    }

}