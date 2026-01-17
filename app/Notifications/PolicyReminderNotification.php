<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class PolicyReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Collection $renewals,
        private readonly Collection $paymentPlans
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Policy & Payment Reminder Digest')
            ->greeting('Hi team,')
            ->line('Here is the daily summary of policies approaching renewal and payments coming due.');

        if ($this->renewals->isNotEmpty()) {
            $mail->line('')
                ->line('**Policies near renewal:**');

            $this->renewals->each(function ($policy) use (&$mail) {
                $client = optional($policy->client)->client_name ?? 'Unknown client';
                $daysUntilExpiry = $policy->end_date ? (int) now()->diffInDays($policy->end_date, false) : null;
                $daysText = $daysUntilExpiry !== null 
                    ? ($daysUntilExpiry < 0 ? abs($daysUntilExpiry) . ' days overdue' : $daysUntilExpiry . ' days remaining')
                    : 'date unknown';
                
                $mail->line(sprintf(
                    '- %s (%s) expires on %s (%s) - Premium: %s',
                    $policy->policy_no,
                    $client,
                    optional($policy->end_date)->format('d-M-Y') ?? 'n/a',
                    $daysText,
                    number_format($policy->premium ?? 0, 2)
                ));
            });
        } else {
            $mail->line('No policies require renewal attention within the configured window.');
        }

        if ($this->paymentPlans->isNotEmpty()) {
            $mail->line('')
                ->line('**Upcoming payment deadlines:**');

            $this->paymentPlans->each(function ($plan) use (&$mail) {
                $policy = optional($plan->schedule)->policy;
                $client = optional(optional($policy)->client)->client_name ?? 'Unknown client';
                $daysUntilDue = $plan->due_date ? (int) now()->diffInDays($plan->due_date, false) : null;
                $daysText = $daysUntilDue !== null 
                    ? ($daysUntilDue < 0 ? abs($daysUntilDue) . ' days overdue' : $daysUntilDue . ' days remaining')
                    : 'date unknown';

                $mail->line(sprintf(
                    '- %s / %s - %s due on %s (%s) - Amount: %s',
                    optional($policy)->policy_no ?? 'Policy ?',
                    $client,
                    $plan->installment_label ?? 'installment',
                    optional($plan->due_date)->format('d-M-Y') ?? 'n/a',
                    $daysText,
                    number_format($plan->amount ?? 0, 2)
                ));
            });
        } else {
            $mail->line('')
                ->line('No payment deadlines within the configured payment window.');
        }

        return $mail->line('')
            ->line('You can adjust lead times via the command options or scheduler.');
    }
}

