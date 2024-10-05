<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $user_id,public $message,public $name=null,public $status=null,public $related_id=null)
    {
        //
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function toArray(object $notifiable): array
    {
        $response = ['message' => $this->message];

        if(isset($this->name))
        {
            if(isset($this->id))
                $response[$this->name.'_id'] = $this->related_id;

            $response['name'] = $this->name;
            $response['status'] = $this->status;
        }

        return $response;
    }
    public function via(object $notifiable): array
    {
        return ['database','broadcast'];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $response = ['message' => $this->message];

        if(isset($this->name))
        {
            if(isset($this->related_id))
                $response[$this->name.'_id'] = $this->related_id;

            $response['name'] = $this->name;
            $response['status'] = $this->status;
        }

        return $response;
    }
    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->user_id);
    }
    public function shouldBroadcast()
    {
        return true;
    }
    public function broadcastAs()
    {
        return 'user.notifications';
    }
}
