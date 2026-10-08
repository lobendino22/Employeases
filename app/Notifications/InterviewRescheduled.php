<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewRescheduled extends Notification
{
    use Queueable;

    public function __construct(public Interview $interview)
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

        $mail = (new MailMessage)
            ->subject('Interview Rescheduled - ' . $title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your interview for "' . $title . '" has been rescheduled.')
            ->line('**New Date & Time:** ' . $interview->scheduled_at->format('F d, Y g:i A'))
            ->line('**Type:** ' . $interview->type_label)
            ->line('**Location:** ' . $interview->location);

        if ($interview->notes) {
            $mail->line('**Notes:** ' . $interview->notes);
        }

        if ($application) {
            $mail->action('View Application', route('jobseeker.applications.show', $application));
        }

        return $mail
            ->line('Please note the new schedule and adjust your plans accordingly. Good luck!')
            ->salutation('Regards, ' . config('app.name'));
    }
}
