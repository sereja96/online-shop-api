<?php

namespace App\Http\Controllers;

use App\Models\Order;

use App\Http\Requests;
use App\Response;

class OrderController extends Controller
{
    public function getMyOrders()
    {
    //    Order::find(1)->products()->detach([1,2]);

        $orders = Order::withAll()
    //        ->my()
            ->get();

        return Response::success($orders);
    }
}
