<?php

namespace app\Services\Auth;

use App\Models\User;

class TokenService
{
    public static function create_access_token(User $user) :string
    {
        $accessToken = $user->createToken('token')->accessToken;

        // Return the access token value
        return $accessToken;
    }
}
