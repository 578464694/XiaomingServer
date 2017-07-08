<?php

namespace app\api\model;

use think\Model;

class BannerItem extends Model
{
    /**
     * 建立与 Image 表的关联模型（一对一）
     * @return \think\model\relation\BelongsTo
     */
    public function img() {
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}
