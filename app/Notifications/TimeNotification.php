<?php

namespace App\Notifications;

use App\Models\Note;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TimeNotification extends Notification
{
    private $note;

    public function __construct(Note $note)
    {
        $this->note = $note;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('Reminder about the note ' . $this->note->header)
                    ->action('View the note', url('/#'.$this->note->id));
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'reminder_text' => 'Reminder about the note ' . $this->note->header,
            'link' => $this->note->id,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
