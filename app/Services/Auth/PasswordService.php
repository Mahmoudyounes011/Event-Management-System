<?php

namespace App\services\Auth;

use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Mail\ForgetPasswordMail;
use App\Services\User\GetUserService;
use App\Services\Auth\TokenService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PasswordService
{
    public function forget_password(ForgetPasswordRequest $request)
    {
        $email = $request->validated()['email'];

        $resetCode = random_int(10000, 99999);

        $exist = DB::table('password_reset_tokens')->where('email',$email)->get();

        if(isset($exist[0]))
            DB::table('password_reset_tokens')->where('email',$email)->update(['token' => $resetCode,'created_at' => now()]);
        else
            DB::table('password_reset_tokens')->insert(['email' => $email,'token' => $resetCode,'created_at' => now()]);

        Mail::to($email)->send(new ForgetPasswordMail($resetCode));
    }

    public function verify_code(ForgetPasswordRequest $request,$resetCode)
    {
        $email = $request->validated()['email'];

        $exist = DB::table('password_reset_tokens')->where('email',$email)->where('token',$resetCode)->get();

        if(!isset($exist[0]))
            return 0;

        $created_at = $exist[0]->created_at;

        $now = now()->subMinutes(30);

        if($created_at <= $now)
            return 0;

        DB::table('password_reset_tokens')->where('email',$email)->update(['created_at' => null]);

        return 1;
    }
}
