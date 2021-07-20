<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Auth;

class NotificationController extends Controller
{
    public function get()
    {
        // return Notification::all();
        $unreadNotifications = Auth::user()->unreadNotifications;
        $fecha_actual = date('Y-m-d');
        foreach ($unreadNotifications as $notification) {
            if ($fecha_actual != $notification->created_at->toDateString()) {
                $notification->markAsRead();
            }
        }
        return Auth::user()->unreadNotifications;
    }
}
