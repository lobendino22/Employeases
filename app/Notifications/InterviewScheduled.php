<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewScheduled extends Notification
{
    use Queueable;

    public function __construct(public Interview $interview)
    {
    }

    /**
     * Mail only: the app keeps its own in-app notifications in the
     * `notifications` table via App\Models\Notification.
     */
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
            ->subject('Interview Scheduled - ' . $title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Good news! An interview has been scheduled for your application for "' . $title . '".')
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
            ->line('Please arrive on time and bring any documents requested by PESO. Good luck!')
            ->salutation('Regards, ' . config('app.name'));
    }
}
