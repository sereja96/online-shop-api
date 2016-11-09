<?php

namespace App\Http\Controllers;

use App\Models\Shop;

use App\Http\Requests;
use App\Response;

class ShopController extends Controller
{
    public static function getMyShops()
    {
        $shops = Shop::with(['image'])
            ->whereMy()
            ->notDeleted()
            ->enabled()
            ->get();

        return Response::success($shops);
    }

    public static function getAllShops()
    {
        $shops = Shop::with(['image'])
            ->notDeleted()
            ->enabled()
            ->get();

        return Response::success($shops);
    }
}
