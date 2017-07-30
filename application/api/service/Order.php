<?php

namespace app\api\service;

use app\api\model\OrderProduct;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\api\model\Order as OrderModel;
use app\api\model\Product as ProductModel;
use app\lib\exception\StockException;
use app\lib\OrderEnum;
use think\Db;
use think\Exception;


class Order
{
    protected $oProducts; // 订单的商品数据
    protected $products; // 从数据库查询出来的商品数据
    protected $uid;         // 用户的 uid

    function __construct()
    {
        $num = func_num_args();
        switch ($num)
        {
            case 2:
                $this->oProducts = $num[0];
                $this->uid = $num[1];
                $this->products = $this->getProductsByOrder($this->oProducts);
                break;
            default:
                break;
        }
    }

    // 建立订单
    public function place($oProducts, $uid)
    {
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $this->uid = $uid;
        // 库存量检测
        $status = $this->getOrderStatus();
        if (!$status['pass']) {
            $status['order_id'] = -1;
            return $status;
        }
        // 订单快照
        $snap = $this->snapOrder($status);
        $order = $this->createOrder($snap);
        $status['pass'] = true;
        return $status;
    }
    // 生成订单号
    public static function makeOrderNo()
    {
        $yCode = array('A','B','C','D','E','F','G','H','I','J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date('d') .
            substr(time(),-5) . substr(microtime(), 2, 5) . sprintf('%02d',mt_rand(0,99));
        return $orderSn;
    }
    // 创建订单
    private function createOrder($snap)
    {
        Db::startTrans();
        try{
            $order = new OrderModel();
            $order->order_no = $this->makeOrderNo();
            $order->user_id = $this->uid;
            $order->total_count = $snap['totalCount'];
            $order->total_price = $snap['orderPrice'];
            $order->status = OrderEnum::UNPAID;
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_items = json_encode($snap['pStatus']);
            $order->snap_address = $snap['snapAddress'];
            $order->save();

            foreach ($this->oProducts as &$p)
            {
                $p['order_id'] = $order->id;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
            return [
                'order_no' => $order->order_no,
                'order_id' => $order->id,
                'create_time' => $order->create_time
            ];
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    // 生成订单快照
    private function snapOrder($status)
    {
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' => [],
            'snapAddress' => null,
            'snapName' => '',
            'snapImg' => ''
        ];
        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapAddress'] = json_encode($this->getAddress());
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];

        if(count($this->products) > 1) {
            $snap['snapName'] .= '等';
        }
        return $snap;
    }

    // 获得用户地址
    private function getAddress()
    {
        $address = UserAddress::get($this->uid);
        if(!$address) {
            throw new  OrderException([
                'msg' => '用户地址不存在，创建订单失败',
                'errorCode' => 60001
            ]);
        }
        return $address;
    }

    /**
     * 检查订单中商品的库存状态
     * @param $orderID
     * @return 订单库存状态数组
     */
    public function checkOrderStock($orderID)
    {
        $orderPrduct = new OrderProduct();
        $this->oProducts = $orderPrduct->where('order_id', '=', $orderID)->select();
        $orderStatus = $this->getOrderStatus();
        return $orderStatus;
    }

    // 获取订单状态
    private function getOrderStatus()
    {
        $orderStatus = [
            'pass' => true, // 下单是否成功
            'orderPrice' => 0, // 订单总价
            'totalCount' => 0, // 订单中商品总数量
            'pStatusArray' => [] // 历史订单中商品的详细信息(id, haveStock, count, name, totalPrice)
        ];
        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus($oProduct['product_id'], $oProduct['count'], $this->products);
            if(!$pStatus['haveStock']) { // 没库存，则订单失败
                $orderStatus['pass'] = false;
            }
            $orderStatus['orderPrice'] += $pStatus['totalPrice'];
            $orderStatus['totalCount'] += $pStatus['count'];
            array_push($orderStatus['pStatusArray'],$pStatus);
        }
        return $orderStatus;
    }
    // 获得商品的状态
    private function getProductStatus($oPID,$oCount,$products)
    {
        $pIndex = -1;
        $productStatus = [ //保存历史订单中单个商品详情
            'id' => 0,
            'haveStock' => false,
            'count' => 0,   // 单个商品的数量
            'name' => '',
            'totalPrice' => 0 // 用户订单中单个商品总价
        ];
        for ($i = 0; $i < count($products); $i++) { //遍历商品是否存在
            if($products[$i]['id'] == $oPID) {
                $pIndex = $i;
                break;
            }
        }
        if($pIndex == -1) { // 商品不存在
            throw new OrderException([
                'msg' => $oPID.'商品不存在'
            ]);
        }
        else {
            $product = $products[$pIndex];
            if($product['stock'] - $oCount > 0) { // 有库存
                $productStatus['haveStock'] = true;
            }
            $productStatus['id'] = $product['id'];
            $productStatus['count'] = $oCount;
            $productStatus['name'] = $product['name'];
            $productStatus['totalPrice'] = $product['price'] * $oCount;
            return $productStatus;
        }
    }

    // 根据 oProducts 查询数据库，得到 products
    protected function getProductsByOrder($oProducts) {
        $oProIDs = []; // 订单中商品 id
        foreach ($oProducts as $oProduct) {
//            $oProIDs[] = $oProducts['product_id'];
            array_push($oProIDs, $oProduct['product_id']);
        }
        $products = ProductModel::all($oProIDs)->visible( // 查询数据，设置显示字段
            ['id','name','price','stock','main_img_url','from']
        )->toArray();
        if(!$products) { // 如果未查询到商品
            throw new OrderException();
        }
        return $products;
    }

    /**
     * 设置订单号
     * 固定 19位
     * @return string
     */
    private function getOrderNo()
    {
        list($t1,$t2) = explode(' ',microtime());
        $t3 = explode('.',$t1 * 10000);
        $token = $t2.$t3[0].(mt_rand(10000,99999));
        $token = str_pad($token,19,"0",STR_PAD_RIGHT);
        return $token;
    }

}