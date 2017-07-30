<?php
namespace app\api\model;

class ProductImage extends BaseModel
{
    protected $hidden = ['id', 'img_id', 'product_id', 'delete_time', 'update_time'];
    /**
     * 关联 Image
     */
    public function imageUrl()
    {
        return $this->belongsTo('Image','img_id','id');
    }

}