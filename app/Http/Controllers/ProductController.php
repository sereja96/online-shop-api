<?php

namespace App\Http\Controllers;

use App\Functions;
use App\Models\Product;
use App\Response;

use App\Http\Requests;

class ProductController extends Controller
{
    public function getProductsByCategory($categoryIds, $search = null)
    {
        $categoryIds = Functions::splitIds($categoryIds);

        $products = Product::withAll()
            ->whereInCategories($categoryIds)
            ->search($search)
            ->enabled()
            ->get();

        return Response::success($products);
    }

    public function getProductsByBrand($brandIds, $search = null)
    {
        $brandIds = Functions::splitIds($brandIds);

        $products = Product::withAll()
            ->whereInBrands($brandIds)
            ->search($search)
            ->enabled()
            ->get();

        return Response::success($products);
    }

    public function getProductById($id)
    {
        $product = Product::withAll()
            ->whereInIds($id)
            ->enabled()
            ->first();

        if ($product) {
            return Response::success($product);
        }

        return Response::error('product_not_found');
    }
}
