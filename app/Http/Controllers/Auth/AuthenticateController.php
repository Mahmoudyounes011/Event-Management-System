<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Event;
use App\Models\PublicEvent;
use App\Notifications\UserNotification;
use App\Services\Auth\LoginService;
use App\Services\Auth\LogoutService;
use App\services\Auth\PasswordService;
use App\Services\Auth\SignUpService;
use App\Services\Notification\SendNotificationService;
use Exception;
use Illuminate\Foundation\Http\FormRequest;

class AuthenticateController extends Controller
{


    public function signup(StoreUserRequest $request, SignUpService $signupService)
    {

        $data = $signupService->create($request);
        return response([
            'status' => 'success',
            'user' => $data['user'],
            'token' => $data['token'],
        ]);
    }

    public function forget_password(ForgetPasswordRequest $request, PasswordService $password)
    {

        try
        {
            $password->forget_password($request);

            return response([
                'status' => 'success',
                'message' => 'The code is sent successfully'
            ]);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

    }

    public function verify_code(ForgetPasswordRequest $request,$resetCode, PasswordService $password)
    {

        try
        {
            $result = $password->verify_code($request,$resetCode);

            return response([
                'status' => 'success',
                'data' => ['verified' => $result]
            ]);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

    }

    public function login(LoginRequest $request, LoginService $loginService)
    {

        $message = 'Reminder to attend ';
        $events = Event::with('user','attenders')->where('date',now()->format('Y-m-d'))->where('start_time','>=',now()->format('H:i:s'))->where('start_time','<=',now()->addMinutes(110)->format('H:i:s'))->where('status','accepted')->get();
        $Pevents = PublicEvent::with('user','attenders')->get();

        foreach($events as $event)
        {
            foreach($event['attenders'] as $attender)
                (new SendNotificationService)->sendNotify($attender,new UserNotification($attender['id'],$message.$event['name']));
        }

        foreach($Pevents as $event)
        {
            foreach($event['attenders'] as $attender)
                (new SendNotificationService)->sendNotify($attender,new UserNotification($attender['id'],$message.$event['name']));
        }

        try
        {
            $data = $loginService->login($request);

            return response([
                'status' => 'success',
                'user' => $data['user'],
                'token' => $data['token'],
                'isOwner' => $data['isOwner']
            ]);
        }
        catch(Exception $e)
        {
            return response([
                'status' => $e->getMessage()
            ]);
        }

    }

    public function logout(LogoutService $logoutService)
    {
        $logoutService->logout();
        return response(['status'=>'success']);
    }
}
