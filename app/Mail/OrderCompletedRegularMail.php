<?php

namespace App\Mail;

use App\Inquiry;
use App\Offer;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCompletedRegularMail extends Mailable
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

    public $representator;

    public $company;

    /**
     * OrderCompletedMail constructor.
     * @param Order $order
     * @param Inquiry $inquiry
     */
    public function __construct(Order $order, Inquiry $inquiry)
    {
        $this->order = $order;
        $this->inquiry = $inquiry;

        $this->company = $order->model('order-regular-details')->company()->first();
        $this->representator = $this->company->representor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('CONT: Nr.' . $this->inquiry->id . ' Paslaugų teikėjo kontaktai')
            ->markdown('emails.order.regular.details')
            ->attachData(
                $this->order->generateInvoice()->output(),
                $this->order->getInvoiceName(),
                ['mime' => 'application/pdf']
            );
    }
}
