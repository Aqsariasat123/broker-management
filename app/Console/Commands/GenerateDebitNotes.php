<?php

namespace App\Console\Commands;

use App\Models\DebitNote;
use App\Models\PaymentPlan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateDebitNotes extends Command
{
    protected $signature = 'debit-notes:generate {--days=7 : Number of days ahead to check for due dates}';

    protected $description = 'Automatically generate debit notes for payment plans approaching their due dates.';

    public function handle(): int
    {
        $daysAhead = (int) $this->option('days');
        $this->info("Checking payment plans due within {$daysAhead} days...");

        // Find payment plans that are due soon and don't have debit notes yet
        $paymentPlans = PaymentPlan::with(['schedule.policy.client', 'debitNotes'])
            ->whereIn('status', ['pending', 'active'])
            ->whereBetween('due_date', [now()->startOfDay(), now()->addDays($daysAhead)->endOfDay()])
            ->get();

        $generated = 0;
        $skipped = 0;

        foreach ($paymentPlans as $plan) {
            // Check if debit note already exists for this payment plan
            $existingDebitNote = $plan->debitNotes()
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existingDebitNote) {
                $this->line("Skipping Payment Plan #{$plan->id} - debit note already exists: {$existingDebitNote->debit_note_no}");
                $skipped++;
                continue;
            }

            // Generate debit note number
            $debitNoteNo = DebitNote::generateDebitNoteNo();

            try {
                DB::beginTransaction();

                $debitNote = DebitNote::create([
                    'payment_plan_id' => $plan->id,
                    'debit_note_no' => $debitNoteNo,
                    'issued_on' => now(),
                    'amount' => $plan->amount,
                    'status' => 'issued',
                ]);

                // Update payment plan status to active if it was pending
                if ($plan->status === 'pending') {
                    $plan->update(['status' => 'active']);
                }

                // Log activity
                \App\Models\AuditLog::log(
                    'create',
                    $debitNote,
                    null,
                    $debitNote->getAttributes(),
                    'Debit note auto-generated for payment plan: ' . ($plan->installment_label ?? 'Instalment #' . $plan->id)
                );

                DB::commit();

                $clientName = $plan->schedule->policy->client->client_name ?? 'Unknown';
                $this->info("Generated debit note {$debitNoteNo} for Payment Plan #{$plan->id} (Client: {$clientName}, Amount: " . number_format($plan->amount, 2) . ")");
                $generated++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Failed to generate debit note for Payment Plan #{$plan->id}: " . $e->getMessage());
            }
        }

        $this->info("Completed: {$generated} debit notes generated, {$skipped} skipped.");

        return Command::SUCCESS;
    }
}
