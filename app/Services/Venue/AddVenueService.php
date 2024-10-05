<?php
namespace App\Services\Venue;

use App\Models\Venue;
use App\Services\Payment\CreateWalletService;

class AddVenueService
{
    public static function add($data)
    {
        $venue = Venue::create($data);

        CreateWalletService::add($venue);

        return $venue;
    }
}
