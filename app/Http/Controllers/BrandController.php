<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Brand;
use App\Response;

class BrandController extends Controller
{
    public function getBrands($search = null)
    {
        $brands = Brand::with('image')
            ->search($search)
            ->notDeleted()
            ->get();

        return Response::success($brands);
    }
}
