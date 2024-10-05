<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddOrderRequest;
use App\Services\Order\EditOrderService;
use App\Services\Order\GetOrderService;
use App\Services\Order\StoreOrderService;
use Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function add(AddOrderRequest $request,$store_id,StoreOrderService $order)
    {
        try
        {
            $order->add($request,$store_id);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => 'the order is sent successfully'
        ]);
    }

    public function store_requests($store_id,Request $request,GetOrderService $order)
    {
        try
        {
            $orders = $order->store_requests($store_id,$request->input('per_page')?$request->input('per_page'):10);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function store_rejects($store_id,Request $request,GetOrderService $order)
    {
        try
        {
            $orders = $order->store_rejects($store_id,$request->input('per_page')?$request->input('per_page'):10);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function store_orders($store_id,Request $request,GetOrderService $order)
    {
        try
        {
            $orders = $order->store_orders($store_id,$request->input('per_page')?$request->input('per_page'):10);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function user_orders(Request $request,GetOrderService $order)
    {
        try
        {
            $orders = $order->user_orders($request->input('per_page')?$request->input('per_page'):10);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function store_reply($order_id,$result,Request $request,EditOrderService $order)
    {
        try
        {
            $order->edit($request,$order_id,$result);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'message' => 'The reply is sent successfully'
        ]);
    }
}
