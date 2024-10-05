<?php

namespace App\Services\User;

use App\Services\User\GetUserService;

class DeleteUserService
{

    public function delete($user_id)
    {
        $user = GetUserService::find($user_id);

        $user->delete();
    }
}
?>
