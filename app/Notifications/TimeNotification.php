<?php

namespace App\Notifications;

use App\Models\Note;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TimeNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $note;

    public function __construct(Note $note)
    {
        $this->note = $note;
    }

    public function via($notifiable)
    {
        return ['broadcast', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('Reminder about the note ' . $this->note->header)
                    ->action('View the note', route('notes') . '/#' . $this->note->id);
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'reminder_text' => 'Reminder about the note ' . $this->note->header,
            'link' => $this->note->id,
        ]);
    }
}
