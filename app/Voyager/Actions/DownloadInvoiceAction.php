<?php

namespace App\Voyager\Actions;

use TCG\Voyager\Actions\AbstractAction;

class DownloadInvoiceAction extends AbstractAction
{
    public function getTitle()
    {
        // Action title which display in button based on current status
        return 'Download Invoice';
    }

    public function getIcon()
    {
        // Action icon which display in left of button based on current status
        return null;
    }

    public function getAttributes()
    {
        // Action button class
        return [
            'class' => 'btn btn-sm btn-primary pull-left ' . ($this->data->payment_status ? '' : 'hidden'),
        ];
    }

    public function shouldActionDisplayOnDataType()
    {
        // show or hide the action button, in this case will show for posts model
        return $this->dataType->slug == 'orders';
    }

    public function getDefaultRoute()
    {
        // URL for action button when click
        return route('orders.invoice', ['order' => $this->data->{$this->data->getKeyName()}]);
    }
}
