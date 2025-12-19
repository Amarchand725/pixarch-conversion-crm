<?php

return [
    // Individual repository bindings generated automatically
    \App\Modules\Lead\Repositories\Contracts\LeadContract::class =>
        \App\Modules\Lead\Repositories\Eloquent\LeadRepository::class,

    \App\Modules\Team\Repositories\Contracts\TeamContract::class =>
        \App\Modules\Team\Repositories\Eloquent\TeamRepository::class,

    \App\Modules\LeadCapture\Repositories\Contracts\LeadCaptureContract::class =>
        \App\Modules\LeadCapture\Repositories\Eloquent\LeadCaptureRepository::class,

    \App\Modules\Campaign\Repositories\Contracts\CampaignContract::class =>
        \App\Modules\Campaign\Repositories\Eloquent\CampaignRepository::class,

    \App\Modules\BusinessSetting\Repositories\Contracts\BusinessSettingContract::class =>
        \App\Modules\BusinessSetting\Repositories\Eloquent\BusinessSettingRepository::class,

    \App\Modules\Role\Repositories\Contracts\RoleContract::class =>
        \App\Modules\Role\Repositories\Eloquent\RoleRepository::class,

    \App\Modules\User\Repositories\Contracts\UserContract::class =>
        \App\Modules\User\Repositories\Eloquent\UserRepository::class,

    \App\Modules\Faq\Repositories\Contracts\FaqContract::class =>
        \App\Modules\Faq\Repositories\Eloquent\FaqRepository::class,

    \App\Modules\Meeting\Repositories\Contracts\MeetingContract::class =>
        \App\Modules\Meeting\Repositories\Eloquent\MeetingRepository::class,

    \App\Modules\ActivityLog\Repositories\Contracts\ActivityLogContract::class =>
        \App\Modules\ActivityLog\Repositories\Eloquent\ActivityLogRepository::class,
];