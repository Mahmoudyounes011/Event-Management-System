<?php

namespace app\Services\Auth;

use App\Http\Requests\StoreUserRequest;
use App\Services\User\StoreUserService;
use App\Services\Auth\TokenService;
use App\Services\Payment\CreateWalletService;

class SignUpService
{
    public function create(StoreUserRequest $request)
    {
        $user = StoreUserService::create($request);
        $token = TokenService::create_access_token($user);
        CreateWalletService::add($user);
        return
        [
            'token' => $token,
            'user' => $user
        ];
    }
}
