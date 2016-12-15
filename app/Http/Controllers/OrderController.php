<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Order;

use App\Http\Requests;
use App\Models\OrderProduct;
use App\Models\User;
use App\Response;
use Illuminate\Support\Facades\Input;

class OrderController extends Controller
{
    public function getMyOrders()
    {
        $orders = Order::withAll()
            ->my()
            ->get();

        return Response::success($orders);
    }

    private function getMyOrderById($id)
    {
        $order = Order::withAll()
            ->my()
            ->where('id', $id)
            ->first();

        return $order;
    }

    private function getSearchDiscount($code)
    {
        return Discount::where('code', $code)
            ->first();
    }

    public function searchDiscount($code)
    {
        $discount = $this->getSearchDiscount($code);

        return $discount
            ? Response::success($discount)
            : Response::error("not_found");
    }

    public function makeOrder()
    {
        $data = Input::all();
        if (empty($data['address']) || empty($data['phone'])) {
            return Response::error("invalid_data");
        }

        $discountId = null;
        if (!empty($data['discount'])) {
            $discount = $this->getSearchDiscount($data['discount']);

            if ($discount) {
                $discountId = $discount->id;
            }
        }

        $order = Order::create([
            'address' => $data['address'],
            'phone' => $data['phone'],
            'discount_id' => $discountId,
            'user_id' => User::myId()
        ]);

        $basketController = new BasketController();
        $basketItems = $basketController->getBasketItems();

        if ($basketItems) {
            foreach ($basketItems as $item)
            {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'count' => $item->count
                ]);
            }
        }

        return Response::success($this->getMyOrderById($order->id));
    }
}
