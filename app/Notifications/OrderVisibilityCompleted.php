<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class OrderVisibilityCompleted extends Notification
{
    use Queueable;

    public $order;

    public $visibility;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $visibility)
    {
        $this->order = $order;
        $this->visibility = $visibility;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', OneSignalChannel::class];
    }

    /**
     * @param $notifiable
     * @return mixed
     */
    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject("Pakeistas matomumas")
            ->body(sprintf('Matomumo vieta: %s', $this->visibility->name));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'Matomumas pakeistas!',
            'type' => __CLASS__,
            'sub_message' => sprintf('Matomumo vieta: %s', $this->visibility->name),
        ];
    }
}
