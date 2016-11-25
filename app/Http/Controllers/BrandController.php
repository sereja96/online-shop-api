<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Brand;
use App\Response;

class BrandController extends Controller
{
    public function getBrands($count = 0, $search = null)
    {
        $brands = Brand::with(['image', 'productCount'])
            ->search($search)
            ->enabled()
            ->takeCount($count)
            ->get();

        return Response::success($brands);
    }

    public function getBrand($id)
    {
        $brand = Brand::withAll()
            ->whereInIds($id)
            ->enabled()
            ->first();

        if ($brand) {
            return Response::success($brand);
        }

        return Response::error('brand_not_found');
    }
}
