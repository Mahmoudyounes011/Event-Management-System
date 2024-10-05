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

class NotifyAttendersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $message,public $event)
    {
        //
    }

    /**
     * Execute the job.
     */
    
    public function handle(): void
    {
        $users = $this->event['attenders'];

        foreach($users as $user)
        {
            (new SendNotificationService)->sendNotify($user,new UserNotification($user->id,$this->message));
        }

        $this->event->delete();
    }
}
