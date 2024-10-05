<?php

namespace app\services\Auth;

use App\Http\Requests\LoginRequest;
use App\Services\User\GetUserService;
use App\Services\Auth\TokenService;
use Exception;

class LoginService
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $this->attempt($credentials);

        $user = GetUserService::find()->makeVisible('email');

        $isOwner = ((isset($user->roles) && isset($user->roles[0]) && $user->roles[0]->role == 'owner') || (isset($user->roles) && isset($user->roles[1]) && $user->roles[1]->role == 'owner')) ? 1 : 0;

        unset($user->roles);

        return ['user' => $user ,'token' => TokenService::create_access_token($user),'isOwner' => $isOwner];

    }

    public function attempt(array $credentials)
    {
        if(!auth()->attempt($credentials))
            throw new Exception('failed');
    }
}
