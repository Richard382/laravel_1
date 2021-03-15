<?php

namespace App\Notifications;

use App\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class ForcedOffer extends Notification
{
    use Queueable;

    /**
     * @var Inquiry
     */
    public $inquiry;

    /**
     * ForcedOffer constructor.
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
            ->subject("Jus pasirinko!")
            ->body("Paspauskite čia, kad pamatytumėte daugiau informacijos!")
            ->url(route('inquiry.index'));
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
            'message' => 'Jus pasirinko!',
            'type' => __CLASS__,
            'link' => route('inquiry.index'),
            'sub_message' => sprintf('Užklausa Nr.: %s', $this->inquiry->id),
        ];
    }
}
