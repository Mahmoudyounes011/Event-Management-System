<?php

namespace App\Console\Commands;

use App\Models\Store;
use App\Models\Venue;
use App\Notifications\UserNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckUnavailableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:check-unavailable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'to check if there are stores or venues not completed there info yet to alert there owners';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $venues = Venue::withoutGlobalScope('available')->where('available',0)->with('owner')->get();
        $stores = Store::withoutGlobalScope('available')->where('available',0)->with('owner')->get();


        foreach($venues as $venue)
            Notification::send($venue['owner'],new UserNotification($venue['owner']['id'],'Your '.$venue['name'].' venue is still unavailable to our users until you comlete its information'));


        foreach($stores as $store)
            Notification::send($store['owner'],new UserNotification($store['owner']['id'],'Your '.$store['name'].' Store is still unavailable to our users until you comlete its information'));
    }
}
