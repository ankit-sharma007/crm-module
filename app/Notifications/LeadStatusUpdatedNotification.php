<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadStatusUpdatedNotification extends Notification
{
    use Queueable;

    protected $lead;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(Lead $lead, string $oldStatus, string $newStatus)
    {
        $this->lead = $lead;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Lead Status Updated')
                    ->line('The status of a lead assigned to you has been updated.')
                    ->line('Lead: ' . $this->lead->first_name . ' ' . $this->lead->last_name)
                    ->line('Old Status: ' . ucfirst($this->oldStatus))
                    ->line('New Status: ' . ucfirst($this->newStatus))
                    ->action('View Lead', url('/leads/' . $this->lead->id))
                    ->line('Please follow up as needed.');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
