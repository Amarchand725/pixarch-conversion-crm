<?php

namespace App\Traits;

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

    // ✅ MANUAL NOTIFICATION RIGHT AFTER SAVE DEMO
    // $assignees = $dba->users; // belongsTo
    // if ($assignees && $assignees->count()) {
    //     foreach ($assignees as $user) {
    //         $link = rtrim(env('FULL_APP_URL'), '/') . '/dbas/' . $dba->uuid;
    //         $dbaName = $dba->name ?? 'N/A';
    //         $dba->notifyUser(
    //             $user,
    //             'DBA Process Status',
    //             "DBA '{$dbaName}' status has been updated.",
    //             $link,
    //             'dba_process_status'
    //         );
    //     }
    // }
}
