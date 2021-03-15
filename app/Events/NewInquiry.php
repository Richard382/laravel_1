<?php

namespace App\Events;

use App\Inquiry;
use Illuminate\Queue\SerializesModels;

class NewInquiry
{
    use SerializesModels;

    /**
     * @var Inquiry
     */
    public $inquiry;

    /**
     * NewInquiry constructor.
     * @param $inquiry Inquiry
     */
    public function __construct($inquiry)
    {
        $this->inquiry = $inquiry;
    }
}
