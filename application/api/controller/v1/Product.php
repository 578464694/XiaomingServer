<?php
namespace app\api\controller\v1;

use app\api\validate\IDMustBePositiveInt;
use think\Controller;
use app\api\validate\Count;
use app\api\model\Product as ProductModel;
use app\lib\exception\ProductMissException;

class Product extends Controller
{
    /**
     * 最近新品
     * @count 新品数量
     * @url /product/recent?count=12
     */
    public function getRecent($count = 15)
    {
        (new Count())->goCheck();
        $products = ProductModel::getMostRecent($count);
        if(!$products) {
            throw new ProductMissException();
        }
        return $products;
    }

    public function getAllInCategory($id = 0)
    {
        (new IDMustBePositiveInt())->goCheck();
        $products = ProductModel::getProductsByCategoryID($id);
        if($products->isEmpty()) {
            throw new ProductMissException();
        }
        $products->hidden(['summary']);
        return $products;
    }

    /**
     * @param int $id
     * @return \app\api\model\product对象及详情
     * @throws json 形式的 product对象
     * @url /product/1
     */
    public function getOne($id = 0)
    {
        (new IDMustBePositiveInt())->goCheck();
        $product = ProductModel::getDetailByID($id);
        if(empty($product))
        {
            throw new ProductMissException();
        }
        return $product;
    }
}