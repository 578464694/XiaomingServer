<?php
namespace app\api\service;

use think\Loader;

Loader::import('wxpay.WxPay',EXTEND_PATH,'.Api.php');

class WxPayNotify extends \WxPayNotify
{
    public function NotifyProcess($data, &$msg)
    {
        // 检查库存
        // 更新订单的 status 状态
        // 减库存
        // 如果成功处理，返回微信成功处理的消息; 否则，我们需要返回没有成功处理

    }
}