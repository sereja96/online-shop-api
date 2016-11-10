<?php

namespace App\Http\Controllers;

use App\CommonModel;
use App\Models\Product;
use App\Response;

use App\Http\Requests;

class ProductController extends Controller
{
    public static function getProductsByCategory($categoryIds, $search = null)
    {
        $categoryIds = CommonModel::splitIds($categoryIds);

        $products = Product::withAll()
            ->whereInCategories($categoryIds)
            ->search($search)
            ->notDeleted()
            ->get();

        return Response::success($products);
    }

    public static function getProductsByBrand($brandIds, $search = null)
    {
        $brandIds = CommonModel::splitIds($brandIds);

        $products = Product::withAll()
            ->whereInBrands($brandIds)
            ->search($search)
            ->notDeleted()
            ->get();

        return Response::success($products);
    }
}
