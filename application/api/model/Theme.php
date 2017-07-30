<?php

namespace app\api\model;

use think\Model;

class Theme extends BaseModel
{
    protected $hidden = ['topic_img_id', 'head_img_id', 'delete_time', 'update_time'];
    /**
     * 建立 theme 表中 topic_img_id 与 image 表 id 的一对一关系
     * @return \think\model\relation\BelongsTo
     */
    public function topicImg()
    {
        return $this->belongsTo('Image', 'topic_img_id', 'id');
    }

    public function headImg()
    {
        return $this->belongsTo('Image', 'head_img_id', 'id');
    }

    /**
     * 建立多对多关联模型
     * @return \think\model\relation\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany('Product', 'theme_product', 'product_id', 'theme_id');
    }

    /**
     * 返回 theme和poducts
     * @id theme id
     * @return theme数据模型
     */
    public static function getThemeWithProducts($id)
    {
        $theme = self::with('products,topicImg,headImg')
                        ->find($id);
        return $theme;
    }
}
