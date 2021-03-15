<?php

namespace App\Mail;

use App\Inquiry;
use App\Offer;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Order
     */
    public $order;

    /**
     * @var Inquiry
     */
    public $inquiry;

    /**
     * OrderCompletedMail constructor.
     * @param Order $order
     * @param Inquiry $inquiry
     */
    public function __construct(Order $order, Inquiry $inquiry)
    {
        $this->order = $order;
        $this->inquiry = $inquiry;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('CONT: Nr.' . $this->inquiry->id . ' informacija ir kliento kontaktai')
            ->markdown('emails.order.details')
            ->attachData(
                $this->order->generateInvoice()->output(),
                $this->order->getInvoiceName(),
                ['mime' => 'application/pdf']
            );
    }
}
