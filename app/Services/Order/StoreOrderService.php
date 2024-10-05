<?php

namespace App\Services\Order;

use App\Models\StoreProduct;
use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use App\Services\Payment\UpdateBalanceService;
use App\Services\Store\GetStoreService;
use App\Services\Time\CheckDateService;
use App\Services\User\GetUserService;
use App\Services\Venue\GetVenueService;
use App\Traits\AssistentFunctions;
use Exception;
use Illuminate\Support\Facades\Hash;

class StoreOrderService
{
    use AssistentFunctions;
    public function add($request,$store_id)
    {
        $data = $request->validated();

        $ids = $request['ids'];

        $quantity = $request['quantity'];

        $this->opposite($ids,$quantity,false,false,'Each product should has quantity not only id');
        $this->opposite($quantity,$ids,false,false,'Each product should has id not only quantity');

        if($request->password==null)
            throw new Exception('You must enter your password');

        $user = GetUserService::find();

        if($request->password!=null && !Hash::check($request->password,$user['password']))
            throw new Exception('Wrong password');

        $store = GetStoreService::find($store_id,['times','owner','wallet']);

        // if($user['id'] == $store['owner']['id'])
        //     throw new Exception('You can not book within your store');


        $day = CheckDateService::get_day($data['date']);

        $times = $store->times_of_day($day)->get();

        $illegal = false;

        foreach($times as $time)
        {
            if((strtotime($data['time'])>=strtotime($time->pivot->start_time)) && (strtotime($data['time'])<=strtotime($time->pivot->end_time)))
            {
                $illegal = true;
                break;
            }
        }

        if(!$illegal)
            throw new Exception('You should book within work time');



        $products = StoreProduct::whereIn('id',$ids)->get();

        $cost = 0;

        foreach($products as $key => $product)
        {
            $cost += $product->price*$quantity[$key];
        }

        if(isset($data['delivery']) && $data['delivery']==1 && $store->hasDelivery==1)
        {
            if(isset($data['venue_id']))
                $venue = GetVenueService::find($data['venue_id']);
            else
            {
                $longitude = $data['longitude'];
                $latitude = $data['latitude'];
            }
            $cost += $store->deliveryCost;
        }


        $owner = $store->owner;

        UpdateBalanceService::pay($user,$cost,$store,true);

        $order = $store->requests()->create(['user_id' => $user->id,'time' => $data['time'],'date' => $data['date']]);

        if(isset($data['delivery']) && $data['delivery']==1 && $store->hasDelivery==1)
        {
            if(isset($venue))
                $order->location()->attach($venue);
            else
                $order->costum_location()->create(['longitude' => $longitude,'latitude' => $latitude]);
        }

        foreach($products as $key => $product)
        {
            $order->contents()->create(['store_product_id' => $product->id,'quantity' => $quantity[$key]]);
        }

        $message = 'Someone sent you an order , check your inbox to display the order';

        (new SendNotificationService)->sendNotify($owner,new UserNotification($owner->id,$message,'Order','Pending'));
    }
}
