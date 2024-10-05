<?php

namespace app\services\Auth;

use App\Services\User\GetUserService;

class LogoutService
{
    public function logout()
    {
        GetUserService::find()->token()->revoke();
    }
}
