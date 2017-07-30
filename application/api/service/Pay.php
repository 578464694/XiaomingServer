<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/26
 * Time: 23:11
 */

namespace app\api\service;


use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use app\lib\OrderStatusEnum;
use think\Exception;
use think\Loader;
use think\Log;

Loader::import('wxpay.WxPay',EXTEND_PATH,'.Api.php');

class Pay
{
    private $orderID;
    private $orderNo;

    function __construct($orderID)
    {
        if (!$orderID) {
            throw new Exception('订单号不能为空');
        }
        $this->orderID = $orderID;
    }

    public function pay()
    {
        // 检查订单是否存在
        // 检查订单 user_id 与 当前用户的 uid 是否一致
        // 检查订单状态是否合法（未支付）
        // 检查库存

        $orderService = new OrderService();
        $status = $orderService->checkOrderStock();
        if (!$status['pass']) {
            return $status;
        }
        return $this->makeWxPreOrder($status['orderPrice']);

    }

    private function makeWxPreOrder($totalPrice)
    {
        //openid
        $openid = Token::getCurrentTokenVar('openid');
        if(!$openid) {
            throw new TokenException();
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNo);  //订单号
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice * 100);  // 交易总金额
        $wxOrderData->SetBody('王尼玛');
        $wxOrderData->SetOpenid($openid); // openid
        $wxOrderData->SetNotify_url(''); //回调
        return $this->getPaySignature($wxOrderData);
    }

    private function getPaySignature($wxOrderData)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if($wxOrder['return_code'] != 'SUCCESS' ||
            $wxOrder['result_code'] != 'SUCCESS')
        {
            Log::record($wxOrder,'error');
            Log::record('获取预支付订单失败','error');
        }
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    private function recodePrePay($wxOrder)
    {
        $order = new OrderModel();
        $result = $order->where('id','=',$this->orderID)->update(['prepay_id' => $wxOrder['prepay_id']]);
        return $result;
    }
    // 签名
    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.appID'));
        $jsApiPayData->SetTimeStamp((string)time()); //转换为 string类型
        $rand = md5(time() . mt_rand(0,1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');

        $sign = $jsApiPayData->MakeSign(); //获得签名
        $rowValues = $jsApiPayData->GetValues(); //获得数组
        $rowValues['paySign'] = $sign; // 将参数和签名返回客户端
        unset($rowValues['appId']);
        $rowValues = array_values($rowValues);
        return $rowValues;
    }

    public function checkOrderValid()
    {
        $uid = Token::getCurrentUID();
        $order = OrderModel::get($uid); // 订单是否存在
        if (!$order) {
            throw new OrderException();
        }
        // 检查订单 user_id 与 当前用户的 uid 是否一致
        if (!Token::checkOperateValid($order->user_id)) {
            throw new OrderException();
        }
        // 检查订单状态是否合法（未支付）
        if ($order->status != OrderStatusEnum::UNPAID) {
            throw new OrderException([
                'msg' => '订单已支付',
                'errorCode' => '80003'
            ]);
        }
        $this->orderNo = $order->order_no;
        return true;
    }


    private function callWxPay()
    {

    }
}