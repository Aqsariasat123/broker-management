<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Policy;
use App\Models\Client;
use App\Models\LifeProposal;
use App\Models\LookupValue;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Adding demo data...');

        // Get current year and month
        $now = Carbon::now();
        $currentYear = $now->year;
        $currentMonth = $now->month;

        // === INCOME DATA ===
        // Skip if demo income already exists
        if (!Income::where('income_code', 'like', 'DEMO%')->exists()) {
            $this->command->info('Adding Income records...');
            $incomeAmounts = [5000, 7500, 12000, 8500, 15000, 9000, 11000, 6500, 14000, 10000, 8000, 13500];
            $baseIncomeId = Income::max('id') ?? 0;
            foreach ($incomeAmounts as $month => $amount) {
                Income::create([
                    'income_code' => 'DEMO' . str_pad($baseIncomeId + $month + 1, 4, '0', STR_PAD_LEFT),
                    'date_received' => Carbon::create($currentYear, $month + 1, rand(1, 28)),
                    'amount_received' => $amount + rand(0, 2000),
                    'description' => 'Commission Income - Month ' . ($month + 1),
                ]);
            }
        } else {
            $this->command->info('Income demo data already exists, skipping...');
        }

        // === EXPENSE DATA ===
        // Skip if demo expense already exists
        if (!Expense::where('expense_code', 'like', 'DEMO%')->exists()) {
            $this->command->info('Adding Expense records...');
            $expenseAmounts = [3000, 4500, 5000, 3500, 6000, 4000, 5500, 3800, 7000, 4500, 3500, 6500];
            $payees = ['Office Rent', 'Utilities', 'Staff Salaries', 'Marketing', 'Insurance Premium', 'Office Supplies'];
            $baseExpenseId = Expense::max('id') ?? 0;
            foreach ($expenseAmounts as $month => $amount) {
                Expense::create([
                    'expense_code' => 'DEMO' . str_pad($baseExpenseId + $month + 1, 4, '0', STR_PAD_LEFT),
                    'payee' => $payees[array_rand($payees)],
                    'date_paid' => Carbon::create($currentYear, $month + 1, rand(1, 28)),
                    'amount_paid' => $amount + rand(0, 1000),
                    'description' => 'Monthly Expense - Month ' . ($month + 1),
                ]);
            }
        } else {
            $this->command->info('Expense demo data already exists, skipping...');
        }

        // Get the max client ID for generating unique clid
        $maxClientId = Client::max('id') ?? 0;

        // Get a source lookup value
        $sourceId = LookupValue::where('name', 'like', '%Referral%')
            ->orWhere('name', 'like', '%Walk%')
            ->orWhere('name', 'like', '%Direct%')
            ->first()?->id ?? 1;

        // === CLIENTS WITH BIRTHDAYS THIS MONTH ===
        // Skip if demo clients already exist
        if (!Client::where('clid', 'like', 'DEMO%')->exists()) {
            $this->command->info('Adding Clients with birthdays this month...');
            $birthdayClients = [
                ['first' => 'John', 'surname' => 'Smith'],
                ['first' => 'Sarah', 'surname' => 'Johnson'],
                ['first' => 'Michael', 'surname' => 'Brown'],
                ['first' => 'Emily', 'surname' => 'Davis'],
                ['first' => 'Robert', 'surname' => 'Wilson'],
            ];

            foreach ($birthdayClients as $index => $client) {
                $maxClientId++;
                Client::create([
                    'first_name' => $client['first'],
                    'surname' => $client['surname'],
                    'client_name' => $client['first'] . ' ' . $client['surname'],
                    'client_type' => 'Individual',
                    'source' => $sourceId,
                    'status' => 'Active',
                    'clid' => 'DEMO' . str_pad($maxClientId, 5, '0', STR_PAD_LEFT),
                    'signed_up' => Carbon::now()->subMonths(rand(1, 12)),
                    'dob_dor' => Carbon::create(1985 + $index, $currentMonth, rand(1, 28)), // Birthday this month
                    'mobile_no' => '071' . rand(1000000, 9999999),
                    'email_address' => strtolower($client['first'] . '.' . $client['surname']) . '@example.com',
                ]);
            }

            // === CLIENTS WITH EXPIRED IDS ===
            $this->command->info('Adding Clients with expired IDs...');
            $expiredClients = [
                ['first' => 'James', 'surname' => 'Miller'],
                ['first' => 'Patricia', 'surname' => 'Taylor'],
                ['first' => 'David', 'surname' => 'Anderson'],
                ['first' => 'Jennifer', 'surname' => 'Thomas'],
            ];
            foreach ($expiredClients as $index => $client) {
                $maxClientId++;
                Client::create([
                    'first_name' => $client['first'],
                    'surname' => $client['surname'],
                    'client_name' => $client['first'] . ' ' . $client['surname'],
                    'client_type' => 'Individual',
                    'source' => $sourceId,
                    'status' => 'Active',
                    'clid' => 'DEMO' . str_pad($maxClientId, 5, '0', STR_PAD_LEFT),
                    'signed_up' => Carbon::now()->subMonths(rand(1, 12)),
                    'dob_dor' => Carbon::create(1980 + $index, 5, 15),
                    'id_expiry_date' => Carbon::now()->subDays(rand(30, 365)), // Expired ID
                    'mobile_no' => '072' . rand(1000000, 9999999),
                    'email_address' => strtolower($client['first'] . '.' . $client['surname']) . '@example.com',
                ]);
            }
        } else {
            $this->command->info('Client demo data already exists, skipping...');
        }

        // Get or create lookup values for policy class
        $lifeClassId = LookupValue::where('name', 'like', '%Life%')->first()?->id;
        $generalClassId = LookupValue::where('name', 'like', '%General%')
            ->orWhere('name', 'like', '%Motor%')
            ->orWhere('name', 'like', '%Property%')
            ->first()?->id;

        // Get first client for policies
        $firstClient = Client::first();
        $clientId = $firstClient?->id ?? 1;

        // Get max policy ID for unique numbering
        $maxPolicyId = Policy::max('id') ?? 0;

        // === LIFE POLICIES ===
        // Skip if demo life policies already exist
        if (!Policy::where('policy_no', 'like', 'LIFE%')->exists()) {
            if ($lifeClassId) {
                $this->command->info('Adding Life Policies...');
                for ($i = 0; $i < 5; $i++) {
                    $startDate = Carbon::now()->subMonths(rand(1, 12));
                    $maxPolicyId++;
                    Policy::create([
                        'client_id' => $clientId,
                        'policy_no' => 'LIFE' . str_pad($maxPolicyId, 4, '0', STR_PAD_LEFT),
                        'policy_code' => 'POL' . str_pad($maxPolicyId + 1000, 6, '0', STR_PAD_LEFT),
                        'policy_class_id' => $lifeClassId,
                        'start_date' => $startDate,
                        'end_date' => Carbon::now()->addMonths(rand(6, 24)),
                        'date_registered' => $startDate,
                        'premium' => rand(500, 2000),
                        'sum_insured' => rand(50000, 500000),
                    ]);
                }
            }
        } else {
            $this->command->info('Life policy demo data already exists, skipping...');
        }

        // === GENERAL POLICIES (expiring this month) ===
        // Skip if demo general policies already exist
        if (!Policy::where('policy_no', 'like', 'GEN%')->exists()) {
            $this->command->info('Adding General Policies expiring this month...');
            for ($i = 0; $i < 3; $i++) {
                $startDate = Carbon::now()->subYear();
                $maxPolicyId++;
                Policy::create([
                    'client_id' => $clientId,
                    'policy_no' => 'GEN' . str_pad($maxPolicyId, 4, '0', STR_PAD_LEFT),
                    'policy_code' => 'POL' . str_pad($maxPolicyId + 2000, 6, '0', STR_PAD_LEFT),
                    'policy_class_id' => $generalClassId,
                    'start_date' => $startDate,
                    'end_date' => Carbon::now()->addDays(rand(1, 30)), // Expiring this month
                    'date_registered' => $startDate,
                    'premium' => rand(1000, 5000),
                    'sum_insured' => rand(100000, 1000000),
                ]);
            }
        } else {
            $this->command->info('General policy demo data already exists, skipping...');
        }

        // === LIFE PROPOSALS ===
        $pendingStatusId = LookupValue::where('name', 'like', '%Pending%')->first()?->id;
        $processingStatusId = LookupValue::where('name', 'like', '%Processing%')->first()?->id;

        // Get or create a contact for the life proposals
        $contact = \App\Models\Contact::first();
        $contactId = $contact?->id ?? 1;

        // Skip if demo life proposals already exist
        if (!LifeProposal::where('proposers_name', 'like', 'Proposer%')->exists()) {
            // Get max proposal ID for unique prid
            $maxProposalId = LifeProposal::max('id') ?? 0;

            if ($pendingStatusId) {
                $this->command->info('Adding Life Proposals (Pending)...');
                for ($i = 0; $i < 3; $i++) {
                    $maxProposalId++;
                    LifeProposal::create([
                        'proposers_name' => 'Proposer Pending ' . ($i + 1),
                        'prid' => 'DEMO-PR' . str_pad($maxProposalId, 5, '0', STR_PAD_LEFT),
                        'contact_id' => $contactId,
                        'status_id' => $pendingStatusId,
                        'sum_assured' => rand(50000, 200000),
                        'premium' => rand(500, 2000),
                        'offer_date' => Carbon::now()->subDays(rand(1, 30)),
                    ]);
                }
            }

            if ($processingStatusId) {
                $this->command->info('Adding Life Proposals (Processing)...');
                for ($i = 0; $i < 2; $i++) {
                    $maxProposalId++;
                    LifeProposal::create([
                        'proposers_name' => 'Proposer Processing ' . ($i + 1),
                        'prid' => 'DEMO-PR' . str_pad($maxProposalId, 5, '0', STR_PAD_LEFT),
                        'contact_id' => $contactId,
                        'status_id' => $processingStatusId,
                        'sum_assured' => rand(50000, 200000),
                        'premium' => rand(500, 2000),
                        'offer_date' => Carbon::now()->subDays(rand(1, 30)),
                    ]);
                }
            }
        } else {
            $this->command->info('Life proposals demo data already exists, skipping...');
        }

        // === COMMISSIONS (OUTSTANDING) ===
        // Skip if demo commissions already exist
        if (!\App\Models\Commission::where('commission_code', 'like', 'DEMO%')->exists()) {
            $this->command->info('Adding Commission records (Outstanding)...');

            // Get an insurer lookup value
            $insurerId = LookupValue::whereHas('lookupCategory', fn($q) => $q->where('name', 'Insurers'))
                ->first()?->id;

            $maxCommissionId = \App\Models\Commission::max('id') ?? 0;

            // Add 5 outstanding commissions (date_received is NULL)
            for ($i = 0; $i < 5; $i++) {
                $maxCommissionId++;
                \App\Models\Commission::create([
                    'commission_code' => 'DEMO' . str_pad($maxCommissionId, 5, '0', STR_PAD_LEFT),
                    'insurer_id' => $insurerId,
                    'basic_premium' => rand(1000, 5000),
                    'rate' => rand(5, 15),
                    'amount_due' => rand(500, 2500),
                    'date_due' => Carbon::now()->subDays(rand(1, 60)),
                    // date_received is NULL = outstanding
                ]);
            }

            // Add 3 received commissions (date_received is set)
            for ($i = 0; $i < 3; $i++) {
                $maxCommissionId++;
                \App\Models\Commission::create([
                    'commission_code' => 'DEMO' . str_pad($maxCommissionId, 5, '0', STR_PAD_LEFT),
                    'insurer_id' => $insurerId,
                    'basic_premium' => rand(1000, 5000),
                    'rate' => rand(5, 15),
                    'amount_due' => rand(500, 2500),
                    'amount_received' => rand(500, 2500),
                    'date_due' => Carbon::now()->subDays(rand(30, 90)),
                    'date_received' => Carbon::now()->subDays(rand(1, 30)),
                ]);
            }
        } else {
            $this->command->info('Commission demo data already exists, skipping...');
        }

        $this->command->info('Demo data added successfully!');
    }
}
