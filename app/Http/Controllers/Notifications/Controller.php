<?php

namespace App\Http\Controllers\Notifications;

use Illuminate\Support\Facades\Auth;

class Controller extends \App\Http\Controllers\Controller
{
    public function read($notification_id)
    {
        $notification = Auth::user()->notifications()->find($notification_id);

        if ($notification)
        {
            $notification->markAsRead();
        }
    }
    public function markview($notification_id)
    {
        $notification = Auth::user()->notifications()->find($notification_id);

        if ($notification)
        {
            $notification->isview = '1';
            $notification->save();
        }
    }
    public function getList()
    {
        $notifications = Auth::user()->unreadNotifications;
        $notviewed = Auth::user()->isAllView();
        return response()->json(["success"=>true, 'notifications'=>$notifications, 'notviewed'=>$notviewed]);
    }
}
