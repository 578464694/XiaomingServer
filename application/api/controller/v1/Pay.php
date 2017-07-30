<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;

class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'preOrder']
    ];
    public function preOrder($id = '')
    {
        (new IDMustBePositiveInt())::goCheck();
        $payService = new PayService();
        $payService->pay();
    }

    public function receiveNotify()
    {
        // 通知频率 15/15/30/180/1800/1800/1800/3600
        // 检查库存
        // 更新订单的 status 状态
        // 减库存
        // 如果成功处理，返回微信成功处理的消息; 否则，我们需要返回没有成功处理

        // 特点：post xml格式：不会携带参数
        
    }
}