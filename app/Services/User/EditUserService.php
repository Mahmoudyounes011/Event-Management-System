<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\User\GetUserService;
use Exception;
use Illuminate\Support\Facades\DB;

class EditUserService
{

    public function reset_password($request)
    {
        $data = $request->validated();

        $email = $data['email'];

        $exist = DB::table('password_reset_tokens')->where('email',$email)->where('created_at',null)->get();

        if(!isset($exist[0]))
            throw new Exception('You have to verify code first');

        $data['password'] = bcrypt($data['password']);

        User::where('email',$email)->update(['password' => $data['password']]);

        DB::table('password_reset_tokens')->where('email',$email)->update(['created_at' => now()->subHour()]);

    }
}
?>
