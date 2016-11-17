<?php

namespace App\Http\Controllers;

use App\Models\Shop;

use App\Http\Requests;
use App\Response;

class ShopController extends Controller
{
    public function getMyShops()
    {
        $shops = Shop::with(['image'])
            ->my()
            ->enabled()
            ->get();

        return Response::success($shops);
    }

    public function getAllShops()
    {
        $shops = Shop::withAll()
            ->enabled()
            ->get();

        return Response::success($shops);
    }
}
