<?php

namespace App\Models\Traits;
use Illuminate\Database\Eloquent\Model;

trait SendsModelNotifications
{
    protected function sendNotification(
        Model $model,
        iterable $users,
        string $title,
        string $message,
        string $type
    ): void {
        $link = rtrim(env('APP_URL'), '/') . '/back-office/' . 'leads/' . $model->uuid;

        $assigner = auth()->user();
        $assignerAvatar = $assigner?->avatar?->path
            ? asset('storage/' . $assigner->avatar->path)
            : asset('back-office/assets/img/avatars/default-avatar.png');

        foreach ($users as $user) {
            if ($user->id === $assigner->id) {
                continue;
            }

            $model->notifyUser(
                $user,
                $assignerAvatar,
                $title,
                $message,
                $link,
                $type
            );
        }
    }
}