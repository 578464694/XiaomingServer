<?php

namespace app\api\model;

use think\Model;

class BaseModel extends Model
{
    protected $autoWriteTimestamp = true;
    protected function prefixImgUrl($value, $data) {
        $finalUrl = $value;
        if($data['from'] == 1)
        {
            $finalUrl = config('setting.img_url_prefix').$value;
        }
        return $finalUrl;
    }
}
