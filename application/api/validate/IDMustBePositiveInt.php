<?php
namespace app\api\validate;


class IDMustBePositiveInt extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger',
    ];

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
            return $field.'必须是正整数';
        }
    }
}