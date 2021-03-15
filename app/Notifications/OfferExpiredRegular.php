<?php

namespace App\Notifications;

use App\Inquiry;
use App\Mail\OfferExpiredRegularMail;
use App\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class OfferExpiredRegular extends Notification
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
            ->subject("Teikėjo pasiūlymas nebuvo apmokėtas")
            ->body("Pranešame, kad paslaugų teikėjas neapmokėjo pasiūlymo!")
            ->url(route('inquiry.index'));
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return OfferExpiredRegularMail
     */
    public function toMail($notifiable)
    {
        return (new OfferExpiredRegularMail($this->offer, $this->inquiry))->to($notifiable->email);
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
            'message' => 'NT specialistas atsisakė Jūsų pasiūlymo.',
            'type' => __CLASS__,
            'link' => route('inquiry.index').'/'.$this->inquiry->id,
            'sub_message' => sprintf('Užklausa Nr.: %s', $this->inquiry->id),
        ];
    }
}
