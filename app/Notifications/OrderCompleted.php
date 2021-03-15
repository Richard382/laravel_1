<?php

namespace App\Notifications;

use App\Inquiry;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class OrderCompleted extends Notification
{
    use Queueable;

    /**
     * @var Inquiry
     */
    public $inquiry;

    /**
     * OrderCompleted constructor.
     * @param Inquiry $inquiry
     */
    public function __construct(Inquiry $inquiry)
    {
        $this->inquiry = $inquiry;
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
            ->subject("Sėkmingas apmokėjimas")
            ->body("Informacija sistemoje buvo sėkmingai apmokėta!");
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
            'message' => 'Sėkmingas apmokėjimas',
            'type' => __CLASS__,
            'sub_message' => sprintf('Užklausa Nr.: %s', $this->inquiry->id),
        ];
    }
}
