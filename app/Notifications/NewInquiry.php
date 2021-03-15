<?php

namespace App\Notifications;

use App\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use Illuminate\Notifications\Notification;

class NewInquiry extends Notification
{
    use Queueable;

    /**
     * @var Inquiry
     */
    public $inquiry;

    /**
     * NewInquiry constructor.
     *
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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
     public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject("Nauja Užklausa")
            ->body("Paspauskite čia, kad pamatytumėte daugiau informacijos!")
            ->url($this->inquiry->getLink());
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
            'message' => 'Nauja Užklausa',
            'type' => __CLASS__,
//            'link' => $this->inquiry->getLink(),
            'link' => url('uzklausos'),
            'sub_message' => sprintf('Užklausa Nr.: %s', $this->inquiry->id),
        ];
    }
//    public function toArray($notifiable)
//    {
//        return [
//            ''
//        ];
//    }
}
