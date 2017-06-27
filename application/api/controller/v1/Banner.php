<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/26
 * Time: 15:21
 */

namespace app\api\controller\v1;
use app\api\validate\IDMustBePositiveInt;
use app\api\model\Banner as BannerModel;
use think\Exception;

class Banner
{
    /**
     * 获取指定 id 的 banner 信息
     * @url /banner/:id
     * @http GET
     * @id banner 的 id 号
     */
    public function getBanner($id)
    {
        (new IDMustBePositiveInt())->goCheck();
//        $banner = BannerModel::getBannerByID($id);
            // 封住错误信息
//            $err = [
//                'error_code' => 10001,
//                'msg' => $ex->getMessage()
//            ];
//            return json($err,400);  //json 第二个参数为 HTTP 的状态
//        return $banner;
//        return 'xxx';
    }
}