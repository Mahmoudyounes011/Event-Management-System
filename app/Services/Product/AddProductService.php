<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Notifications\UserNotification;
use App\Services\Image\ImageService;
use App\Services\Notification\SendNotificationService;
use App\Services\Store\GetStoreService;
use App\Services\User\UserVerificationService;
use App\Traits\AssistentFunctions;
use Exception;

class AddProductService
{
    use AssistentFunctions;

    public function add($store_id,$request)
    {
        $store = GetStoreService::find($store_id,['times'],true);

        $data = $request->validated();

        $images = $request->file('images');

        $data = $this->validation($store['user_id'],$data,$images);

        $prices = $data['prices'];

        $names = $data['names'];

        $descriptions = $data['descriptions'];

        $products_ids = $data['products_ids'];

        if($products_ids)
        {
            $products = (new GetAllService)->find($products_ids);
            if(count($products) != count($products_ids))
                throw new Exception('Some products Ids are invalid');
        }

        foreach($prices as $key => $price)
        {
            if(isset($products_ids) && isset($products_ids[$key]))
                $store->products()->create(['product_id' => $products_ids[$key],'price' => $price]);
            else
            {
                $product = Product::create(['name' => $names[$key],'description' => $descriptions[$key]]);


                $paths = (new ImageService)->upload_image($images[$key],'product');
                foreach($paths as $path)
                    $product->photos()->create(['path' => $path]);

                $store->products()->create(['product_id' => $product->id,'price' => $price]);
            }
        }

        if(isset($store['times']) && isset($store['times'][0]))
        {
            $store->available = 1;
            $store->save();

            $message = 'Hello '.$store['owner']['name'].' your store '.$store['name'].' became visible to all users now';

            (new SendNotificationService)->sendNotify($store['owner'],new UserNotification($store['owner']['id'],$message));

        }

    }

    private function validation($owner_id,$data,$images)
    {
        UserVerificationService::verify($owner_id);

        $validated = [];

        $names = null;

        $descriptions = null;

        $products_ids = null;

        $prices = $data['prices'];


        if(isset($data['products_ids']))
        $products_ids = $data['products_ids'];


        if(isset($data['names']))
        {

            $names = $data['names'];

            $this->opposite($names,$images,false,false,'Each product has both name and images not only name');

            $descriptions = $data['descriptions'];

            $this->opposite($names,$descriptions,false,false,'Each product has both name and description not only name');
            $this->opposite($descriptions,$names,false,false,'Each product has both name and description not only description');
            $this->opposite($prices,$names,false,false,'Each product has both price and id not only price',$products_ids);
            $this->opposite($names,$prices,false,false,'Each product has both price and name not only name');

            if(isset($products_ids))
            {
                $this->opposite($products_ids,$prices,false,false,'Each product has both price and id not only id',$names);
            }
        }
        else
        {
            $this->opposite($prices,$products_ids,false,false,'Each product has both price and id not only price');
            $this->opposite($products_ids,$prices,false,false,'Each product has both price and id not only id');
        }


        $validated['prices'] = $prices;
        $validated['products_ids'] = $products_ids;
        $validated['names'] = $names;
        $validated['descriptions'] = $descriptions;

        return $validated;
    }
}
