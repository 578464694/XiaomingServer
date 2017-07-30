<?php
namespace app\api\model;


class User extends BaseModel
{
    // 建立与 user_address 的关联关系
    public function address()
    {
        return $this->hasOne('UserAddress', 'user_id', 'id');
    }

    public static function getByOpenID($openID)
    {
        $user = self::where('openid','=',$openID)->find();
        return $user;
    }
}