<?php

namespace app\api\validate;

use app\lib\exception\ParameterException;

class OrderPlace extends BaseValidate
{
//    protected $products = [
//        [
//            'product_id' => 1,
//            'count' => 2
//        ],[],[],
//    ];

    // 校验是否是数组，是否为空，
    // 校验元素是否是正整数

    protected $rule = [
        'products' => 'checkProducts'
    ];

    // 校验 product
    protected $singleRule = [
        'product_id' => 'isPositiveInteger',
        'count' => 'isPositiveInteger'
    ];
    // 校验 products 数组
    public function checkProducts($value, $rule = '', $data = '', $field = '')
    {
        if(empty($value)) {
            throw new ParameterException(
                [
                    'msg' => 'products不能为空'
                ]);
        }
        if(!is_array($value)) {
            throw new ParameterException(
                [
                    'msg' => 'products应为数组'
                ]);
        }
        foreach ($value as $key => $product) {
            $validate = new BaseValidate($this->singleRule);
            $result = $validate->check($product); // 借助 basevalidate 的 正整数验证，校验product
            if(!$result) {
                throw new ParameterException(
                    [
                        'msg' =>'products参数不合法'
                    ]);
            }
        }
        return true;
    }

}