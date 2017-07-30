<?php

namespace app\api\model;

use think\Model;

class Image extends BaseModel
{
    protected $hidden = ['id', 'from', 'delete_time', 'update_time'];

    /**
     * 读取器
     * @param $url
     * @param $data 读取的一条记录
     * @return string
     */
    public function getUrlAttr($url, $data) {
        return $this->prefixImgUrl($url, $data);
    }

}
