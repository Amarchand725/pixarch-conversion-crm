<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $assigner_avatar;
    public $title;
    public $message;
    public $url;
    public $type;
    public $extra;

    public function __construct($assigner_avatar, $title, $message, $url = null, $type = 'info', $extra = [])
    {
        $this->assigner_avatar = $assigner_avatar;
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->type = $type;
        $this->extra = $extra;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //         ->subject($this->title)
    //         ->view('emails.system_notification', [
    //             'assigner_avatar' => $this->assigner_avatar,
    //             'title' => $this->title,
    //             'message' => $this->message,
    //             'url' => $this->url,
    //             'type' => $this->type,
    //             'extra' => $this->extra,
    //         ]);
    // }

    public function toDatabase($notifiable)
    {
        return [
            'assigner_avatar' => $this->assigner_avatar,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'type' => $this->type,
            'extra' => $this->extra,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'assigner_avatar' => $this->assigner_avatar,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'type' => $this->type,
            'extra' => $this->extra,
        ]);
    }
}
