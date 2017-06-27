<?php
namespace app\api\model;

use think\Exception;
use think\Model;

class Banner extends Model
{
    public static function getBannerByID($id)
    {
        //TODO::根据 ID 返回 banner 相应信息
        try {
            1 / 0;
        }
        catch (Exception $ex) {
            throw $ex;
        }
        return 'this is banner info';
    }
}