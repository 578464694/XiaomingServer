<?php
namespace app\api\controller;

use think\Controller;
use app\api\service\Token as TokenService;

class BaseController extends Controller
{
    // 用户和管理员访问权限
    protected function checkPrimaryScope()
    {
        return TokenService::beforePrimaryScope();
    }

    // 用户访问权限
    protected function checkExclusiveScope()
    {
        return TokenService::beforeExclusiveScope();
    }

}