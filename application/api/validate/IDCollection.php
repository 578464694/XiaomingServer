<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/9
 * Time: 16:50
 */

namespace app\api\validate;


class IDCollection extends BaseValidate
{
    protected $rule = [
        'ids' => 'require|checkIDs'
    ];

    protected $message = [
        'ids' => 'ids必须是以逗号分割的整数'
    ];

    protected function checkIDs($value)
    {
        $ids = explode(',', $value);
        // 非空
        if(empty($ids))
        {
            return false;
        }
        // 正整数
        foreach ($ids as $id)
        {
            if(!$this->isPositiveInteger($id))
            {
                return false;
            }
        }
        return true;
    }
}