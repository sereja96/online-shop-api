<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Response;
use Illuminate\Http\Request;

use App\Http\Requests;

class ProductController extends Controller
{
    public static function getProductsByCategory($categoryIds)
    {
        $categories = explode(',', $categoryIds);
        if (!is_array($categories)) {
            $categories = [$categories];
        }

        $products = Product::withAll()
            ->whereInCategories($categories)
            ->notDeleted()
            ->get();

        return Response::success($products);
    }

    public static function getProductsByBrand($brandIds)
    {
        $brands = explode(',', $brandIds);
        if (!is_array($brands)) {
            $brands = [$brands];
        }

        $products = Product::withAll()
            ->whereInBrands($brands)
            ->notDeleted()
            ->get();

        return Response::success($products);
    }
}
