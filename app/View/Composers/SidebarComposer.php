<?php

namespace App\View\Composers;

use App\Models\Meeting;
use App\Models\Role;
use App\Models\User;
use App\Modules\ActivityLog\Models\ActivityLog;
use App\Modules\Campaign\Models\Campaign;
use App\Modules\Faq\Models\Faq;
use App\Modules\Lead\Models\Lead;
use App\Modules\LeadCapture\Models\LeadCapture;
use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view)
    {
        $leadCount = 0;
        $meetingCount = 0;
        $notificationCount = 0;

        if (auth()->check()) {
            $user = auth()->user();

            if ($user->hasRole('Admin')) {
                $leadCount = Lead::count();
                $meetingCount = Meeting::count();
            } else {
                $leadCount = $user->leads()->count();
                $meetingCount = $user->meetings()->count();
            }

            $notificationCount = $user->unreadNotifications()->count();
        }

        $view->with('sidebarCounts', [
            'notifications' => $notificationCount,
            'leads'         => $leadCount,
            'faqs'          => Faq::count(),
            'lead_captures' => LeadCapture::count(),
            'campaigns'     => Campaign::count(),
            'users'         => User::count(),
            'roles'         => Role::count(),
            'activity_logs' => ActivityLog::count(),
            'meetings'      => $meetingCount,
        ]);
    }
}