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

    public $title;
    public $message;
    public $url;
    public $type;
    public $extra;

    public function __construct($title, $message, $url = null, $type = 'info', $extra = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->type = $type;
        $this->extra = $extra;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toMail($notifiable)
    {
        // return (new MailMessage)
        //     ->subject($this->title)
        //     ->view('emails.system_notification', [
        //         'title' => $this->title,
        //         'message' => $this->message,
        //         'url' => $this->url,
        //         'type' => $this->type,
        //         'extra' => $this->extra,
        //     ]);

        // return (new MailMessage)
        //     ->subject($this->title)
        //     ->markdown('emails.system_notification', [
        //         'title' => $this->title,
        //         'message' => $this->message,
        //         'url' => $this->url,
        //         'type' => $this->type,
        //         'extra' => $this->extra,
        //     ]);

        $mail = (new MailMessage)
                ->subject($this->title)
                ->line($this->message);

            if ($this->url) {
                $mail->action('View', $this->url);
            }

            return $mail->line('Thank you for using our application!');
    }

    public function toDatabase($notifiable)
    {
        return [
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
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'type' => $this->type,
            'extra' => $this->extra,
        ]);
    }
}
