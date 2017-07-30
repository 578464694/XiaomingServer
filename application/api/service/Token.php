<?php

namespace app\api\service;

use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use app\lib\ScopeEnum;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    /**
     * 生成 token
     * @return string
     */
    protected function generateToken()
    {
        $randChars = getRandChars(32);
        $request_time = $_SERVER['REQUEST_TIME_FLOAT'];
        $salt = config('secure.salt');
        return md5($randChars . $request_time . $salt);
    }

    /**
     * 规定 token 放在 http header 中
     * 根据 key 从当前的缓存中取值
     * @param $key
     */
    public static function getCurrentTokenVar($key)
    {
        // 根据 token 来获取 uid
        $token = Request::instance()->header('token');
        if (!$token) {
            throw new TokenException(['msg' => 'token未传递']);
        }
        $vars = Cache::get($token);
        if (!$vars) {   // 判断 cache 是否存在
            throw new TokenException();
        } else {
            // 根据 key ，从 cache取出对应值
            if (!is_array($vars)) {
                $cache = json_decode($vars, true);
            }
            if (array_key_exists($key, $cache)) { // 判断 key 是否存在
                return $cache[$key];
            } else {
                throw new Exception('尝试获取的 Token变量不存在');
            }
        }
    }

    // 获得当前 UID
    public static function getCurrentUID()
    {
        return self::getCurrentTokenVar('uid');
    }

    // 只有用户才能访问的接口权限
    public static function beforeExclusiveScope()
    {
        // 获得当前用户权限
        $scope = self::getCurrentTokenVar('scope');
        if (!$scope)
        {
            throw new TokenException();
        }
        else
        {
            if ($scope == ScopeEnum::USER)
            {
                return true;
            }
            else // 用户权限不足
            {
                throw new ForbiddenException();
            }
        }
    }
    // 用户和管理员都能访问的接口权限
    public static function beforePrimaryScope()
    {
        // 获得当前用户权限
        $scope = self::getCurrentTokenVar('scope');
        if(!$scope)
        {
            throw new TokenException();
        }
        else
        {
            if($scope >= ScopeEnum::USER)
            {
                return true;
            }
            else // 用户权限不足
            {
                throw new ForbiddenException();
            }
        }
    }
    // 检查 uid 是否是当前用户的 uid
    public static function checkOperateValid($checkedUID)
    {
        $uid = self::getCurrentUID();
        if($uid === $checkedUID) {
            return true;
        }
        else
        {
            return false;
        }
    }
}