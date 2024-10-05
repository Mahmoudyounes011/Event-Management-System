<?php

namespace App\Services\User;

use Exception;

class UserVerificationService
{

    public static function verify($owner_id)
    {
        if(auth()->user()->id !== $owner_id)
            throw new Exception('You are not allowed to access this service');
    }
}
