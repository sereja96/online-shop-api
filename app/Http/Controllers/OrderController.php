<?php

namespace App\Http\Controllers;

use App\Models\Order;

use App\Http\Requests;
use App\Response;

class OrderController extends Controller
{
    public function getMyOrders()
    {
        $orders = Order::withAll()
            ->my()
            ->get();

        return Response::success($orders);
    }
}
