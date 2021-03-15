<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderVisibilityCompleted extends Mailable
{
    use Queueable, SerializesModels;

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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('CONT: Pakeistas matomumas')
            ->markdown('emails.order.details-visibility')
            ->attachData(
                $this->order->generateInvoice()->output(),
                $this->order->getInvoiceName(),
                ['mime' => 'application/pdf']
            );
    }
}
