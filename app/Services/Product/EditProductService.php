<?php

namespace App\Services\Product;

use App\Models\StoreProduct;
use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use App\Services\User\UserVerificationService;
use Exception;

class EditProductService
{

    public function edit($new_price,$product_id)
    {
        if(!is_numeric($new_price) || $new_price<=0)
            throw new Exception('Price must be a positive numeric value');

        $store_product = StoreProduct::where('available',1)->with('store')->find($product_id);

        if(!isset($store_product))
            throw new Exception('Product not found');

        UserVerificationService::verify($store_product['store']['user_id']);

        if($store_product->price == $new_price)
            throw new Exception('You have entered the same price');

        $store_product->available = 0;

        $store_product['store']->products()->create(['product_id' => $store_product->product_id,'price' => $new_price,'created_at' => now()]);

        $store_product->save();

    }

    public function make_hidden($product_id)
    {
        $store_product = StoreProduct::where('available',1)->with('store.products','store.owner')->find($product_id);

        if(!isset($store_product))
            throw new Exception('Product not found');

        UserVerificationService::verify($store_product['store']['user_id']);

        $store_product->available = 0;

        $store_product->save();

        if(count($store_product['store']['products']) <= 1 )
        {
            $store_product['store']->available = 0;

            $store_product['store']->save();

            $message = 'Your store will be unvisible to our users , please add at least one product to make it visible';

            (new SendNotificationService)->sendNotify($store_product['store']['owner'],new UserNotification($store_product['store']['user_id'],$message,'Store','Unavailable'));
        }
    }


}
