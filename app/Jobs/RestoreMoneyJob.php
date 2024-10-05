<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use App\Services\Payment\UpdateBalanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RestoreMoneyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $event)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = $this->event['attenders'];

        $owner = $this->event['user'];

        $balance = $this->event['ticket']['price'];

        foreach($users as $user)
        {
            (new UpdateBalanceService)->addAfterReject($balance,$user,$owner);
        }
    }
}
