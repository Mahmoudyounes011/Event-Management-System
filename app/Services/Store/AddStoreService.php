<?php
namespace App\Services\Store;

use App\Models\Store;
use App\Services\Payment\CreateWalletService;
use Exception;

class AddStoreService
{
    public static function add($data)
    {
        $store = Store::create($data);

        CreateWalletService::add($store);

        return $store;
    }
}
