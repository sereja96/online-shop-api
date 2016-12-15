<?php

namespace App\Http\Controllers;

use App\Models\Shop;

use App\Http\Requests;
use App\Response;

class ShopController extends Controller
{
    public function getMyShops($count = null)
    {
        $shops = Shop::with(['image'])
            ->my()
            ->enabled()
            ->takeCount($count)
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

    public function getPopularShops($count = 6, $search = null)
    {
        $shops = Shop::with(['image', 'productCount'])
            ->enabled()
            ->orderBy('rate', 'DESC')
            ->search($search)
            ->takeCount($count)
            ->get();

        return Response::success($shops);
    }

    public function getShop($id)
    {
        $shop = Shop::withAll()
            ->whereInIds($id)
            ->enabled()
            ->first();

        return Response::success($shop);
    }
}
