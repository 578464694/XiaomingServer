<?php
namespace app\api\controller\v1;


use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ThemeException;
use app\lib\exception\ThemeMissException;

class Theme
{
    /**
     * @ids 请求的主题 id
     * @url theme?ids=id1,id2,id3...
     * @return 一组 theme 模型
     */
    public function getSimpleList($ids = '')
    {
        (new IDCollection())->goCheck();
        $ids = explode(',', $ids);
        $themes = ThemeModel::with('topicImg,headImg')->select($ids);
        if($themes->isEmpty())
        {
            throw new ThemeMissException();
        }
        return $themes;
    }

    /**
     * @url theme/id=1
     * @return theme 模型
     */
    public function getComplexOne($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $theme = ThemeModel::getThemeWithProducts($id);
        if(!$theme) {
            throw new ThemeMissException();
        }
        return $theme;
    }

}