<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Interview $interview, public string $previousStatus)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $interview = $this->interview;
        $application = $interview->application;
        $vacancy = $application?->jobVacancy;
        $title = $vacancy?->title ?? 'the position you applied for';

        $statusMessages = [
            'completed' => 'Your interview has been marked as completed',
            'cancelled' => 'Your interview has been cancelled',
            'rescheduled' => 'Your interview has been rescheduled',
        ];

        $message = $statusMessages[$interview->status] ?? 'Your interview status has been updated';

        $mail = (new MailMessage)
            ->subject('Interview Status Update - ' . $title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($message . ' for "' . $title . '".')
            ->line('**Updated Status:** ' . $interview->status_label)
            ->line('**Date & Time:** ' . $interview->scheduled_at->format('F d, Y g:i A'))
            ->line('**Type:** ' . $interview->type_label)
            ->line('**Location:** ' . $interview->location);

        if ($interview->notes) {
            $mail->line('**Notes:** ' . $interview->notes);
        }

        if ($application) {
            $mail->action('View Application', route('jobseeker.applications.show', $application));
        }

        return $mail
            ->line('If you have any questions, please contact PESO.')
            ->salutation('Regards, ' . config('app.name'));
    }
}
