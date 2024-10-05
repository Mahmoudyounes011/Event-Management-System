<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyAllUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $message)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::with('roles')->get();

        foreach($users as $user)
        {
            $isAdmin = false;
            foreach($user->roles as $role)
                if($role?->role === 'admin')
                    $isAdmin = true;
            if(!$isAdmin)
                (new SendNotificationService)->sendNotify($user,new UserNotification($user->id,$this->message));
        }
    }
}
