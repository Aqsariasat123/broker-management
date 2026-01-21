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

        // === EXPENSE DATA ===
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

        // === CLIENTS WITH BIRTHDAYS THIS MONTH ===
        $this->command->info('Adding Clients with birthdays this month...');
        $names = ['John Smith', 'Sarah Johnson', 'Michael Brown', 'Emily Davis', 'Robert Wilson'];
        foreach ($names as $index => $name) {
            Client::create([
                'client_name' => $name,
                'dob_dor' => Carbon::create(1985 + $index, $currentMonth, rand(1, 28)), // Birthday this month
                'mobile_no' => '071' . rand(1000000, 9999999),
                'email_address' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
            ]);
        }

        // === CLIENTS WITH EXPIRED IDS ===
        $this->command->info('Adding Clients with expired IDs...');
        $expiredNames = ['James Miller', 'Patricia Taylor', 'David Anderson', 'Jennifer Thomas'];
        foreach ($expiredNames as $index => $name) {
            Client::create([
                'client_name' => $name,
                'dob_dor' => Carbon::create(1980 + $index, 5, 15),
                'id_expiry_date' => Carbon::now()->subDays(rand(30, 365)), // Expired ID
                'mobile_no' => '072' . rand(1000000, 9999999),
                'email_address' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
            ]);
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

        // === LIFE POLICIES ===
        if ($lifeClassId) {
            $this->command->info('Adding Life Policies...');
            for ($i = 0; $i < 5; $i++) {
                Policy::create([
                    'client_id' => $clientId,
                    'policy_no' => 'LIFE' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'policy_code' => 'POL' . str_pad(300 + $i, 6, '0', STR_PAD_LEFT),
                    'policy_class_id' => $lifeClassId,
                    'start_date' => Carbon::now()->subMonths(rand(1, 12)),
                    'end_date' => Carbon::now()->addMonths(rand(6, 24)),
                    'premium' => rand(500, 2000),
                    'sum_insured' => rand(50000, 500000),
                ]);
            }
        }

        // === GENERAL POLICIES (expiring this month) ===
        $this->command->info('Adding General Policies expiring this month...');
        for ($i = 0; $i < 3; $i++) {
            Policy::create([
                'client_id' => $clientId,
                'policy_no' => 'GEN' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'policy_code' => 'POL' . str_pad(400 + $i, 6, '0', STR_PAD_LEFT),
                'policy_class_id' => $generalClassId,
                'start_date' => Carbon::now()->subYear(),
                'end_date' => Carbon::now()->addDays(rand(1, 30)), // Expiring this month
                'premium' => rand(1000, 5000),
                'sum_insured' => rand(100000, 1000000),
            ]);
        }

        // === LIFE PROPOSALS ===
        $pendingStatusId = LookupValue::where('name', 'like', '%Pending%')->first()?->id;
        $processingStatusId = LookupValue::where('name', 'like', '%Processing%')->first()?->id;

        if ($pendingStatusId) {
            $this->command->info('Adding Life Proposals (Pending)...');
            for ($i = 0; $i < 3; $i++) {
                LifeProposal::create([
                    'proposers_name' => 'Proposer Pending ' . ($i + 1),
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
                LifeProposal::create([
                    'proposers_name' => 'Proposer Processing ' . ($i + 1),
                    'status_id' => $processingStatusId,
                    'sum_assured' => rand(50000, 200000),
                    'premium' => rand(500, 2000),
                    'offer_date' => Carbon::now()->subDays(rand(1, 30)),
                ]);
            }
        }

        $this->command->info('Demo data added successfully!');
    }
}
