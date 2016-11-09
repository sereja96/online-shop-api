<?php

namespace App\Http\Controllers;

use App\CommonModel;
use App\Models\Product;
use App\Response;

use App\Http\Requests;

class ProductController extends Controller
{
    public static function getProductsByCategory($categoryIds)
    {
        $categoryIds = CommonModel::splitIds($categoryIds);

        $products = Product::withAll()
            ->whereInCategories($categoryIds)
            ->notDeleted()
            ->get();

        return Response::success($products);
    }

    public static function getProductsByBrand($brandIds)
    {
        $brandIds = CommonModel::splitIds($brandIds);

        $products = Product::withAll()
            ->whereInBrands($brandIds)
            ->notDeleted()
            ->get();

        return Response::success($products);
    }
}
