<?php

namespace app\api\model;

use app\lib\exception\ProductMissException;
use think\Model;

class Product extends BaseModel
{
    protected $hidden = ['delete_time', 'from', 'create_time', 'update_time', 'img_id', 'pivot'];

    public function getMainImgUrlAttr($url, $data) {
        return $this->prefixImgUrl($url, $data);
    }

    /**
     * 关联图片
     */
    public function images()
    {
        return $this->hasMany('ProductImage','product_id','id');
    }

    /**
     * 关联属性
     */
    public function properties()
    {
        return $this->hasMany('ProductProperty','product_id','id');
    }


    /**
     * 查找最近新品
     * @count
     * @return 一组 product模型
     */
    public static function getMostRecent($count)
    {
        $products = self::limit($count)
                        ->order(['create_time' => 'desc'])
                        ->select();
        if($products->isEmpty())
        {
            throw new ProductMissException();
        }
        $products = $products->hidden(['summary']);
        return $products;
    }

    public static function getProductsByCategoryID($id)
    {
        $products = self::where('category_id', '=', $id)->select();
        return $products;
    }

    /**
     * 获得产品详情
     * @param $id
     * @return product对象及详情
     */
    public static function getDetailByID($id)
    {
        $product = self::with([
            'images' => function($query){
                $query->with(['imageUrl'])
                    ->order('order', 'asc');
            }])
            ->with(['properties'])
            ->find($id);
        return $product;
    }




}
