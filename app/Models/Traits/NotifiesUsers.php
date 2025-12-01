<?php

namespace App\Models\Traits;

use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

trait NotifiesUsers
{
    /**
     * Notify multiple users.
     */
    public function notifyUsers($users, $title, $message, $url = null, $type = 'info', $extra = [])
    {
        $users = is_array($users) ? collect($users) : collect([$users]);
        Notification::send($users, new SystemNotification($title, $message, $url, $type, $extra));
    }

    /**
     * Notify a single user.
     */
    public function notifyUser($user, $title, $message, $url = null, $type = 'info', $extra = [])
    {
        $this->notifyUsers([$user], $title, $message, $url, $type, $extra);
    }
}
