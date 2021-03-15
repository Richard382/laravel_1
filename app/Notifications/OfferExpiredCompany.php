<?php

namespace App\Notifications;

use App\Inquiry;
use App\Mail\OfferExpiredMail;
use App\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class OfferExpiredCompany extends Notification
{
    use Queueable;

    /**
     * @var Inquiry
     */
    public $inquiry;

    /**
     * @var Offer
     */
    public $offer;

    /**
     * Create a new notification instance.
     *
     * @param Offer $offer
     * @return void
     */
    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
        $this->inquiry = $offer->inquiry;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail', OneSignalChannel::class];
    }

    /**
     * @param $notifiable
     * @return mixed
     */
    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject("Pasiūlymas nebegalioja")
            ->body("Paspauskite čia, kad pamatytumėte daugiau informacijos!")
            ->url(route('inquiry.index'));
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return OfferExpiredMail
     */
    public function toMail($notifiable)
    {
        return (new OfferExpiredMail($this->offer, $this->inquiry))->to($notifiable->email);
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
            'message' => 'Pasiūlymas nebegalioja',
            'type' => __CLASS__,
            'link' => route('inquiry.index'),
            'sub_message' => sprintf('Užklausa Nr.: %s', $this->inquiry->id),
        ];
    }
}
