<?php

namespace App\Services\Order;

use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use App\Services\Payment\UpdateBalanceService;
use App\Services\User\UserVerificationService;
use Exception;

class EditOrderService
{

    public static function edit($request,$order_id,$result)
    {
        $order = GetOrderService::find($order_id,['products','user','store.owner','store.wallet'],'pending');

        $user = $order['user'];

        $owner = $order['store']['owner'];

        UserVerificationService::verify($owner->id);

        $reasone = $request->reasone;

        $message = 'Hello '.$user->name.", Your order in ".$order->created_at;

        if($result=='accept')
        {
            if(isset($reasone))
                throw new Exception('You can not send reasone while you are accepted the request');

            $order->status = 'accepted';

            $message = $message." has been accepted";

        }
        else
        {
            $order->status = 'rejected';

            $message = $message." has been rejected .";

            if(isset($reasone))
                $message = $message."The reasone is : ".$reasone.'.';

            $message = $message."The cost of this order will be added to your card";

            $balance = 0;

            $products = $order['products'];

            foreach($products as $product)
                $balance+=($product->price*$product->pivot->quantity);

            (new UpdateBalanceService)->addAfterReject($balance,$user,$order['store'],true);
        }

        $order->save();

        (new SendNotificationService)->sendNotify($user,new UserNotification($user->id,$message,'Order',$result));




    }
}
