<?php
namespace app\api\service;


use app\api\model\User as UserModel;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use app\lib\ScopeEnum;
use think\Cache;
use think\Exception;


class UserToken extends Token
{
    protected $code = '';
    protected $wxAppID = '';
    protected $wxAppSecret = '';
    protected $wxLoginUrl = '';

    public function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wx.appID');
        $this->wxAppSecret = config('wx.appSecret');
        $this->wxLoginUrl = sprintf(config('wx.wxBaseUrl'), $this->wxAppID, $this->wxAppSecret, $this->code);
    }

    public function get()
    {
        //https://api.weixin.qq.com/sns/
        //jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code
        $result = curlGet($this->wxLoginUrl);
        $wxResult = (array)json_decode($result);
        if (empty($wxResult)) {
            throw new Exception('获取 session key 及 appID异常，微信内部错误');
        }
        else {
            $loginFail = array_key_exists('errcode', $wxResult);
            if ($loginFail) {
                $this->processLoginError($wxResult);
            }
            else {
                return $this->grantToken($wxResult);
            }

        }

    }

    /**
     * 封装异常信息
     * @param $wxResult
     * @throws WeChatException
     */
    private function processLoginError($wxResult)
    {
        throw new WeChatException([
            'msg' => $wxResult['errmsg'],
            'errorCode' => $wxResult['errcode']
        ]);
    }

    /**
     * 授予 token
     * @param $wxResult
     * @return string
     */
    private function grantToken($wxResult)
    {
        // 拿到openid
        // 查数据库，这个 openid 是否存在
        // 如果存在，则不处理，如果不存在，新增一条 user记录
        // 生成令牌，准备缓存数据，写入缓存
        // 把令牌返回到客户端去
        // key ：token
        // value ： wxResult uid scope
        $openid = $wxResult['openid'];
        $user = UserModel::getByOpenID($openid);
        $uid = 0;
        if (!$user) {
            $uid = $this->createUser($openid);
        } else {
            $uid = $user->id;
        }
        $cacheValue = $this->prepareCacheValue($wxResult, $uid); //准备缓存数据
        $key = $this->saveToCache($cacheValue);
        return $key;
    }
    /**
     * 保存到 cache
     * @param $cacheValue
     * @return array
     */
    private function saveToCache($cacheValue)
    {
        $key = $this->generateToken();  //生成 token
        $value = json_encode($cacheValue);  // 变成 json 字符串
        $expire_in = config('setting.token_expire_in'); //获取失效期
        $result = cache($key,$value,$expire_in);
        if(!$result) {
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $key;
    }

    /**
     * 保存用户到 user表
     * @param $openid
     * @return mixed
     */
    private function createUser($openid)
    {
//        $new_user = new UserModel();
//        $new_user->openid = $openid;
//        $u_id = $new_user->save();
        // 保存数据的另一种写法
        $user = UserModel::create([
            'openid' => $openid
        ]);
        return $user->id;
    }

    /**
     * 包装 cache 数据
     * @param $wxResult
     * @param $uid
     * @return mixed
     */
    private function prepareCacheValue($wxResult, $uid)
    {
        $cacheValue = $wxResult;
        $cacheValue['uid'] = $uid;
        $cacheValue['scope'] = ScopeEnum::USER;
        return $cacheValue;
    }
}
