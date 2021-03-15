<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MenuComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if ( ! Auth::check()) {
            return;
        }

        $view->with('inquiries_count', Auth::user()->getInquiryCount());
    }
}
