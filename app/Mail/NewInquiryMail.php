<?php

namespace App\Mail;

use App\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Inquiry
     */
    public $inquiry;

    /**
     * NewInquiryMail constructor.
     *
     * @param Inquiry $inquiry
     */
    public function __construct(Inquiry $inquiry)
    {
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
            ->subject('CONT: Jūsų Užklausa Nr. ' . $this->inquiry->id . ' sukurta')
            ->markdown('emails.inquiry.new');
    }
}
