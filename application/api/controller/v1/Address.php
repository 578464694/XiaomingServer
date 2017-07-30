<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/24
 * Time: 14:25
 */

namespace app\api\controller\v1;


use app\api\model\User as UserModel;
use app\api\service\Token;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use app\lib\exception\UserException;
use app\lib\exception\SuccessMessage;
use app\api\validate\AddressNew as AddressValidate;
use app\lib\ScopeEnum;
use think\Controller;

class Address extends Controller
{
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress']
    ];

    public function createOrUpdateAddress()
    {
        // 根据 token 来获取 uid
        // 根据 uid 来查找用户数据，判断用户是否存在，如果不存在抛出异常
        // 获取用户从客户端提交来的地址信息
        // 根据用户地址信息是否存在，从而判断是添加地址还是更新地址
        //接收name，mobile，province，city，country，detail，token
        $validate = new AddressValidate();
        $validate->goCheck();


        $uid = Token::getCurrentUID(); //从缓存，获得 uid

        $user = UserModel::get($uid);         // 查询用户数据, 获得用户地址，判断是更新还是修改
        if (!$user) {
            throw new UserException();
        }
        $userAddress = $user->address;
        $datas = $validate->getDatasByRule(input('post.'));

        if(!$userAddress) { // 创建数据
            $user->address()->save($datas);
        }
        else { // 更新数据
            $user->address->save($datas);
        }
//        throw new SuccessException();
        return json(new SuccessMessage(),201);
    }
}