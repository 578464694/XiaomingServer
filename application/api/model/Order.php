<?php
namespace app\api\model;


use app\lib\exception\OrderException;

class Order extends BaseModel
{
    protected function getSnapItemsAttr($value,$data)
    {
        return json_decode($value);
    }

    protected function getSnapAddressAttr($value, $data)
    {
        return json_decode($value);
    }
    /**获得用户的订单
     * @param $uid
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getOrder($id,$uid)
    {
        $order = self::where('id','=',$id)->where('user_id','=',$uid)->find();
        if(!$order) {
            throw new OrderException();
        }
        return $order;
    }

    public static function getSummaryByUser($uid, $page, $size)
    {
       $paginate = self::where('user_id','=',$uid)
           ->order('create_time','desc')
           ->paginate($size,true,[
            'page' => $page
        ]);
       return $paginate;
    }
}