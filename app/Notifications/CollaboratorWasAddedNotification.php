<?php

namespace App\Notifications;

use App\Models\Note;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollaboratorWasAddedNotification extends Notification
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
            ->line($this->note->owner->email . " has shared with you a note '" . $this->note->header . "'")
            ->action("Go to the note", route('collaborator.index') . '/#' . $this->note->id);

        $message->subject = 'Notes App - You are now a collaborator of the note #' . $this->note->id;

        return $message;
    }
}
