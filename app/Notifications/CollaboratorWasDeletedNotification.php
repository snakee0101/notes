<?php

namespace App\Notifications;

use App\Models\Note;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollaboratorWasDeletedNotification extends Notification
{
    private $note;

    public function __construct(Note $note)
    {
        $this->note = $note;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->line("You are now not a collaborator of the note '" . $this->note->header . "'");

        $message->subject = 'Notes App - You are now not a collaborator of the note #' . $this->note->id;

        return $message;
    }
}
