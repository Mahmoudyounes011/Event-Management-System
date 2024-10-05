<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Services\Store\GetStoreService;
use App\Services\User\GetUserService;
use App\Services\User\UserVerificationService;
use Exception;

class GetOrderService
{

    public static function find($order_id,$with=null,$status=null)
    {
        if(isset($with))
            $order = Order::with($with);
        if(isset($status))
            $order = $order->where('status',$status);

        if(isset($order))
            $order = $order->find($order_id);
        else
            $order = Order::find($order_id);


        if(!isset($order))
            throw new Exception('Order not found');

        return $order;

    }

    public static function store_requests($store_id,$paginate=10)
    {
        $store = GetStoreService::find($store_id);

        if(!GetUserService::find()->isAdmin())
            UserVerificationService::verify($store->user_id);

        $orders = $store->requests()->paginate($paginate);

        if(!isset($orders[0]))
            throw new Exception('There are no orders');

        return $orders;
    }

    public static function store_rejects($store_id,$paginate=10)
    {
        $store = GetStoreService::find($store_id);

        if(!GetUserService::find()->isAdmin())
            UserVerificationService::verify($store->user_id);

        $orders = $store->rejects()->paginate($paginate);

        if(!isset($orders[0]))
            throw new Exception('There are no orders');

        return $orders;
    }

    public static function store_orders($store_id,$paginate=10)
    {
        $store = GetStoreService::find($store_id);

        if(!GetUserService::find()->isAdmin())
            UserVerificationService::verify($store->user_id);

        $orders = $store->orders()->paginate(10);

        if(!isset($orders[0]))
            throw new Exception('There are no orders');

        return $orders;
    }

    public static function user_orders($paginate=10)
    {
        $user = GetUserService::find();

        $orders = $user->orders()->with(['products.product','store' => function($query)
        {
            $query->with('phones')->select(['id','name']);
        }])->where('status','!=','rejected')->paginate($paginate);

        foreach($orders as $order)
        {
            $cost = 0;

            foreach($order['products'] as $product)
                $cost += $product['price']*$product['pivot']['quantity'];

            $order['cost'] = $cost;
        }

        if(!isset($orders[0]))
            throw new Exception('There are no orders');

        return $orders;
    }
}
