<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\PublicEvent;
use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use Illuminate\Console\Command;

class EventReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:event-reminder-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
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
    }
}
