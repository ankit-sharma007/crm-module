<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadAssignedNotification extends Notification
{
    use Queueable;

    protected $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Lead Assigned to You')
                    ->line('A new lead has been assigned to you.')
                    ->line('Lead: ' . $this->lead->first_name . ' ' . $this->lead->last_name)
                    ->line('Email: ' . $this->lead->email)
                    ->action('View Lead', url('/leads/' . $this->lead->id))
                    ->line('Thank you for managing this lead!');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
