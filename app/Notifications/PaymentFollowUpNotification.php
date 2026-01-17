<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class PaymentFollowUpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Collection $overdueDebitNotes
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Payment Follow-Up: Overdue Payments Requiring Attention')
            ->greeting('Hi team,')
            ->line('The following payments are overdue and require follow-up action:');

        $this->overdueDebitNotes->each(function ($debitNote) use (&$mail) {
            $client = $debitNote->paymentPlan->schedule->policy->client->client_name ?? 'Unknown';
            $policy = $debitNote->paymentPlan->schedule->policy->policy_no ?? 'N/A';
            $totalPaid = $debitNote->payments->sum('amount');
            $remaining = $debitNote->amount - $totalPaid;
            $daysOverdue = (int) now()->diffInDays($debitNote->issued_on);

            $mail->line('')
                ->line("**{$debitNote->debit_note_no}**")
                ->line("- Client: {$client}")
                ->line("- Policy: {$policy}")
                ->line("- Amount Due: " . number_format($debitNote->amount, 2))
                ->line("- Amount Paid: " . number_format($totalPaid, 2))
                ->line("- Remaining: " . number_format($remaining, 2))
                ->line("- Days Overdue: {$daysOverdue}")
                ->line("- Issued On: " . $debitNote->issued_on->format('d-M-Y'));
        });

        $mail->line('')
            ->action('View Debit Notes', url('/debit-notes'))
            ->line('Please follow up with clients to ensure timely payment.');

        return $mail;
    }
}
