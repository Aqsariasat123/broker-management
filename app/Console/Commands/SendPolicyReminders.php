<?php

namespace App\Console\Commands;

use App\Models\PaymentPlan;
use App\Models\Policy;
use App\Models\AuditLog;
use App\Notifications\PolicyReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendPolicyReminders extends Command
{
    protected $signature = 'policies:send-reminders {--days=30} {--payment-days=14}';

    protected $description = 'Dispatch email reminders for upcoming policy renewals and payment deadlines.';

    public function handle(): int
    {
        $renewalWindow = now()->addDays((int) $this->option('days'));
        $paymentWindow = now()->addDays((int) $this->option('payment-days'));

        $renewals = Policy::with('client')
            ->whereNotNull('end_date')
            ->where('renewable', true) // Only renewable policies
            ->whereBetween('end_date', [now()->startOfDay(), $renewalWindow->endOfDay()])
            ->get();

        $paymentPlans = PaymentPlan::with(['schedule.policy.client'])
            ->where('status', 'pending')
            ->whereBetween('due_date', [now()->startOfDay(), $paymentWindow])
            ->get();

        if ($renewals->isEmpty() && $paymentPlans->isEmpty()) {
            $this->info('No renewals or payment deadlines within the configured window.');
            return Command::SUCCESS;
        }

        $recipient = config('mail.from.address');

        if (!$recipient) {
            $this->warn('No mail.from.address configured; skipping reminder dispatch.');
            return Command::SUCCESS;
        }

        Notification::route('mail', $recipient)
            ->notify(new PolicyReminderNotification($renewals, $paymentPlans));

        // Log activity
        AuditLog::log(
            'reminder_sent',
            null,
            null,
            [
                'renewals_count' => $renewals->count(),
                'payment_plans_count' => $paymentPlans->count(),
                'recipient' => $recipient,
                'renewal_window_days' => (int) $this->option('days'),
                'payment_window_days' => (int) $this->option('payment-days'),
            ],
            sprintf(
                'Policy reminders sent: %d renewals, %d payment deadlines',
                $renewals->count(),
                $paymentPlans->count()
            )
        );

        $this->info("Policy reminder email sent to {$recipient}.");
        $this->info("Summary: {$renewals->count()} policy renewal(s), {$paymentPlans->count()} payment deadline(s).");

        return Command::SUCCESS;
    }
}

