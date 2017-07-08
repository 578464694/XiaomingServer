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
use app\lib\exception\BannerMissException;
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
        // 查询方法 get,find,select,all
        $banner = BannerModel::getBannerByID($id);
        if(!$banner) {
            throw new BannerMissException('内部错误');
        }
        return $banner;
    }

}