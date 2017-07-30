<?php

namespace app\api\controller\v1;

use app\api\service\UserToken;
use app\api\validate\TokenGet;
use think\Controller;

class Token extends Controller
{
    /**
     * @url api/v1/token/user
     * @param string $code
     * @return string
     */
    public static function getToken($code = '')
    {
        (new TokenGet())->goCheck();
        $ut = new UserToken($code);
        $token = $ut->get($code);
        return [
            'token' => $token
        ];
    }

}