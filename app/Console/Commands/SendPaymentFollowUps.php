<?php

namespace App\Console\Commands;

use App\Models\DebitNote;
use App\Models\AuditLog;
use App\Notifications\PaymentFollowUpNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendPaymentFollowUps extends Command
{
    protected $signature = 'payments:send-followups {--days-overdue=7 : Number of days overdue before sending follow-up}';

    protected $description = 'Send automated follow-up messages for overdue payments.';

    public function handle(): int
    {
        $daysOverdue = (int) $this->option('days-overdue');
        $this->info("Checking for payments overdue by {$daysOverdue} days or more...");

        // Find overdue debit notes that haven't been paid
        $overdueDebitNotes = DebitNote::with(['paymentPlan.schedule.policy.client', 'payments'])
            ->whereIn('status', ['issued', 'overdue', 'partial'])
            ->where('issued_on', '<=', now()->subDays($daysOverdue))
            ->get()
            ->filter(function ($debitNote) {
                // Only include if not fully paid
                $totalPaid = $debitNote->payments->sum('amount');
                return $totalPaid < $debitNote->amount;
            });

        if ($overdueDebitNotes->isEmpty()) {
            $this->info('No overdue payments requiring follow-up.');
            return Command::SUCCESS;
        }

        $recipient = config('mail.from.address');
        if (!$recipient) {
            $this->warn('No mail.from.address configured; skipping follow-up dispatch.');
            return Command::SUCCESS;
        }

        // Update status to overdue if not already
        foreach ($overdueDebitNotes as $debitNote) {
            if ($debitNote->status !== 'overdue') {
                $debitNote->update(['status' => 'overdue']);
            }
        }

        // Send notification
        Notification::route('mail', $recipient)
            ->notify(new PaymentFollowUpNotification($overdueDebitNotes));

        // Log activity
        AuditLog::log(
            'payment_followup_sent',
            null,
            null,
            [
                'overdue_count' => $overdueDebitNotes->count(),
                'recipient' => $recipient,
                'days_overdue' => $daysOverdue,
                'total_amount' => $overdueDebitNotes->sum('amount'),
            ],
            sprintf(
                'Payment follow-up sent for %d overdue payment(s)',
                $overdueDebitNotes->count()
            )
        );

        $this->info("Payment follow-up email sent to {$recipient} for " . $overdueDebitNotes->count() . " overdue payment(s).");

        return Command::SUCCESS;
    }
}
