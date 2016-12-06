<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Basket;
use App\Models\Product;
use App\Models\User;
use App\Response;

class BasketController extends Controller
{
    public function getBasket()
    {
        $basket = Basket::withAll()
            ->my()
            ->get();

        return Response::success($basket);
    }

    public function addProduct($productId, $count = 1)
    {
        $product = Product::find($productId);

        if (!$product) {
            return Response::error('product_not_found');
        }

        $basketProduct = Basket::where('product_id', $productId)
            ->my()
            ->first();

        if (!$basketProduct) {
            $basketProduct = Basket::create([
                'user_id' => User::myId(),
                'product_id' => $productId,
                'count' => $count
            ]);
        }

        if ($basketProduct) {
            return Response::success($basketProduct);
        }

        return Response::error('unknown_error');
    }

    public function removeProduct($productId)
    {
        $basketProduct = Basket::where('product_id', $productId)
            ->my()
            ->first();

        if ($basketProduct) {
            $basketProduct->delete();
        }

        return Response::success();
    }

    public function changeProductCount($basketId, $count)
    {
        $basketProduct = Basket::where('id', $basketId)
            ->my()
            ->first();

        if (!$basketProduct) {
            return Response::error('product_not_found');
        }

        $basketProduct->count = $count >= 1
            ? $count
            : 1;

        return $basketProduct->saveOrFail()
            ? Response::success()
            : Response::error();
    }

}
