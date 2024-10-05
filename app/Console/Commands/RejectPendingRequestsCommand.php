<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Order;
use App\Services\Event\GetEventService;
use App\Services\Payment\UpdateBalanceService;
use Illuminate\Console\Command;

class RejectPendingRequestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reject-pending-requests';

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
        $events = Event::with('user','section')->where('status','pending')->where('created_at','<',now()->subMinutes(1))->get();
        $orders = Order::with('products','user','store.owner','store.wallet')->where('status','pending')->where('created_at','<',now()->subMinutes(1))->get();
        foreach($events as $event)
        {
            $event->status = 'reject';
            $event->save();
            $balance = (isset($event['pivot']) && isset($event['pivot']['level'])) ? $event['section']['price']+$event['pivot']['level']['price'] : $event['section']['price'];

            (new UpdateBalanceService)->addAfterReject($balance,$event['user'],$event['section']['venue'],true);
        }

        foreach($orders as $order)
        {
            $order->status = 'rejected';
            $order->save();
            $balance = 0;

            $products = $order['products'];

            foreach($products as $product)
                $balance+=($product->price*$product->pivot->quantity);

            (new UpdateBalanceService)->addAfterReject($balance,$order['user'],$order['store'],true);
        }
    }
}
