<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\service\Token;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\validate\PaginateParameter;
use think\Controller;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;

class Order extends BaseController
{
    // 用户在选择商品后，向 API 提交包含他所选择商品的相关信息\
    // API 在接受到信息后，需要检查订单相关商品的库存量
    // 有库存，则添加到订单表，告诉客户端 ，可以支付
    // 调用支付接口进行支付
    // 支付时，还需检查库存量
    // 库存够，服务器调用微信支付接口进行支付（异步）
    // 根据微信返回的支付结果判断
    // 成功：[库存量检测]
    // 成功：减少库存量，失败：不减少库存量

    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getSummaryByUser,detail']
    ];

    /**
     * 创建订单
     * @return array
     */
    public function placeOrder()
    {
        echo 1;
        (new OrderPlace())->goCheck();
        $oProducts = input('post.products/a');
        $uid = Token::getCurrentUID();
        $orderService = new OrderService();
        $status = $orderService->place($oProducts,$uid);
        return $status;
    }

    public function getSummaryByUser($page = 1, $size = 15)
    {
        (new PaginateParameter())->goCheck();
        $uid = Token::getCurrentUID();
        $paginate = OrderModel::getSummaryByUser($uid, $page, $size);
        $paginate->hidden(['snap_items','snap_address','prepay_id']);
        if($paginate->isEmpty()) {
            return [
                'data' => [],
                'current_page' => $paginate->getCurrentPage()
            ];
        }
        else {
            return [
                'data' => $paginate->toArray(),
                'current_page' => $paginate->getCurrentPage()
            ];
        }
    }
    // 根据订单 id 查看详情
    public function getDetail($id = 0)
    {
        (new IDMustBePositiveInt())->goCheck();
        $uid = Token::getCurrentUID();
        $order = OrderModel::getOrder($id,$uid);
        return $order;
    }
}