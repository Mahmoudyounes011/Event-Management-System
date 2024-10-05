<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use App\services\User\GetUserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PromotionNotifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $m;
    public function __construct(public $eventune)
    {
        $this->m = 'Search for '.$eventune->name.' ,you may be happey to register with it';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::with('roles')->get();

        $owner = GetUserService::find();

        foreach($users as $user)
        {
            $isAdmin = false;
            foreach($user->roles as $role)
            {
                if($role->role === 'admin')
                    $isAdmin = true;
            }
            if(!$isAdmin && ($owner?->id != $user->id))
                (new SendNotificationService)->sendNotify($user,new UserNotification($user->id,$this->m));

        }
    }

    public function fail($exception = null)
    {
        info($exception->getMessage());
    }
}

