<?php
namespace app\api\controller\v1;

use think\Controller;
use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryMissException;

class Category extends Controller
{
    public function getAllCategories()
    {
        $categories = CategoryModel::all([],['img']);
        if($categories->isEmpty())
        {
            throw new CategoryMissException();
        }
        return $categories;
    }
}