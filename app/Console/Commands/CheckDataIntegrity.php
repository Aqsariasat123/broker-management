<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckDataIntegrity extends Command
{
    protected $signature = 'db:check-integrity {--fix : Attempt to fix integrity issues}';

    protected $description = 'Check database integrity including foreign keys, orphaned records, and data consistency.';

    public function handle(): int
    {
        $this->info('Starting database integrity check...');
        $this->newLine();

        $issues = [];
        $fixed = 0;

        // 1. Check foreign key constraints
        $this->info('Checking foreign key constraints...');
        $fkIssues = $this->checkForeignKeys();
        $issues = array_merge($issues, $fkIssues);

        // 2. Check orphaned records
        $this->info('Checking for orphaned records...');
        $orphanedIssues = $this->checkOrphanedRecords();
        $issues = array_merge($issues, $orphanedIssues);

        // 3. Check data consistency
        $this->info('Checking data consistency...');
        $consistencyIssues = $this->checkDataConsistency();
        $issues = array_merge($issues, $consistencyIssues);

        // 4. Check for null values in required fields
        $this->info('Checking required fields...');
        $nullIssues = $this->checkRequiredFields();
        $issues = array_merge($issues, $nullIssues);

        // Summary
        $this->newLine();
        $this->info('=== Integrity Check Summary ===');
        
        if (empty($issues)) {
            $this->info('✓ No integrity issues found. Database is healthy.');
            return Command::SUCCESS;
        }

        $this->warn("Found " . count($issues) . " integrity issue(s):");
        $this->newLine();

        foreach ($issues as $index => $issue) {
            $this->line(($index + 1) . ". [{$issue['type']}] {$issue['table']}: {$issue['message']}");
            if (isset($issue['count'])) {
                $this->line("   Affected records: {$issue['count']}");
            }
        }

        // Attempt to fix if --fix flag is provided
        if ($this->option('fix')) {
            $this->newLine();
            $this->info('Attempting to fix issues...');
            $fixed = $this->fixIssues($issues);
            $this->info("Fixed {$fixed} issue(s).");
        } else {
            $this->newLine();
            $this->comment('Run with --fix flag to attempt automatic fixes.');
        }

        return Command::SUCCESS;
    }

    private function checkForeignKeys(): array
    {
        $issues = [];
        $connection = config('database.default');

        if ($connection === 'mysql' || $connection === 'mariadb') {
            // Check foreign key constraints
            $constraints = DB::select("
                SELECT 
                    TABLE_NAME,
                    CONSTRAINT_NAME,
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            foreach ($constraints as $constraint) {
                $table = $constraint->TABLE_NAME;
                $column = $constraint->COLUMN_NAME;
                $refTable = $constraint->REFERENCED_TABLE_NAME;
                $refColumn = $constraint->REFERENCED_COLUMN_NAME;

                // Check for orphaned records
                $orphaned = DB::select("
                    SELECT COUNT(*) as count
                    FROM {$table} t
                    LEFT JOIN {$refTable} r ON t.{$column} = r.{$refColumn}
                    WHERE t.{$column} IS NOT NULL
                    AND r.{$refColumn} IS NULL
                ");

                if ($orphaned[0]->count > 0) {
                    $issues[] = [
                        'type' => 'Foreign Key',
                        'table' => $table,
                        'message' => "Orphaned records in {$column} referencing {$refTable}.{$refColumn}",
                        'count' => $orphaned[0]->count,
                        'fix' => "DELETE FROM {$table} WHERE {$column} IS NOT NULL AND {$column} NOT IN (SELECT {$refColumn} FROM {$refTable})"
                    ];
                }
            }
        }

        return $issues;
    }

    private function checkOrphanedRecords(): array
    {
        $issues = [];

        // Check common relationships
        $checks = [
            ['table' => 'policies', 'column' => 'client_id', 'ref_table' => 'clients', 'ref_column' => 'id'],
            ['table' => 'schedules', 'column' => 'policy_id', 'ref_table' => 'policies', 'ref_column' => 'id'],
            ['table' => 'payment_plans', 'column' => 'schedule_id', 'ref_table' => 'schedules', 'ref_column' => 'id'],
            ['table' => 'debit_notes', 'column' => 'payment_plan_id', 'ref_table' => 'payment_plans', 'ref_column' => 'id'],
            ['table' => 'payments', 'column' => 'debit_note_id', 'ref_table' => 'debit_notes', 'ref_column' => 'id'],
            ['table' => 'users', 'column' => 'role_id', 'ref_table' => 'roles', 'ref_column' => 'id'],
        ];

        foreach ($checks as $check) {
            if (!Schema::hasTable($check['table']) || !Schema::hasTable($check['ref_table'])) {
                continue;
            }

            try {
                $count = DB::table($check['table'])
                    ->whereNotNull($check['column'])
                    ->whereNotIn($check['column'], function($query) use ($check) {
                        $query->select($check['ref_column'])
                            ->from($check['ref_table']);
                    })
                    ->count();

                if ($count > 0) {
                    $issues[] = [
                        'type' => 'Orphaned Record',
                        'table' => $check['table'],
                        'message' => "Found {$count} orphaned record(s) in {$check['column']}",
                        'count' => $count,
                        'fix' => "DELETE FROM {$check['table']} WHERE {$check['column']} IS NOT NULL AND {$check['column']} NOT IN (SELECT {$check['ref_column']} FROM {$check['ref_table']})"
                    ];
                }
            } catch (\Exception $e) {
                // Table or column might not exist, skip
            }
        }

        return $issues;
    }

    private function checkDataConsistency(): array
    {
        $issues = [];

        // Check payment plan amounts match debit note totals
        if (Schema::hasTable('payment_plans') && Schema::hasTable('debit_notes')) {
            try {
                $inconsistent = DB::select("
                    SELECT pp.id, pp.amount as plan_amount, 
                           COALESCE(SUM(dn.amount), 0) as total_debit_notes
                    FROM payment_plans pp
                    LEFT JOIN debit_notes dn ON pp.id = dn.payment_plan_id
                    GROUP BY pp.id, pp.amount
                    HAVING ABS(pp.amount - COALESCE(SUM(dn.amount), 0)) > 0.01
                ");

                foreach ($inconsistent as $record) {
                    $issues[] = [
                        'type' => 'Data Consistency',
                        'table' => 'payment_plans',
                        'message' => "Payment Plan #{$record->id} amount ({$record->plan_amount}) doesn't match total debit notes ({$record->total_debit_notes})",
                        'count' => 1,
                    ];
                }
            } catch (\Exception $e) {
                // Skip if query fails
            }
        }

        // Check debit note amounts match payment totals
        if (Schema::hasTable('debit_notes') && Schema::hasTable('payments')) {
            try {
                $inconsistent = DB::select("
                    SELECT dn.id, dn.debit_note_no, dn.amount as note_amount,
                           COALESCE(SUM(p.amount), 0) as total_payments
                    FROM debit_notes dn
                    LEFT JOIN payments p ON dn.id = p.debit_note_id
                    GROUP BY dn.id, dn.debit_note_no, dn.amount
                    HAVING ABS(dn.amount - COALESCE(SUM(p.amount), 0)) > 0.01
                    AND dn.status != 'cancelled'
                ");

                foreach ($inconsistent as $record) {
                    $issues[] = [
                        'type' => 'Data Consistency',
                        'table' => 'debit_notes',
                        'message' => "Debit Note {$record->debit_note_no} amount ({$record->note_amount}) doesn't match total payments ({$record->total_payments})",
                        'count' => 1,
                    ];
                }
            } catch (\Exception $e) {
                // Skip if query fails
            }
        }

        return $issues;
    }

    private function checkRequiredFields(): array
    {
        $issues = [];

        // Check for null values in critical fields
        $checks = [
            ['table' => 'clients', 'column' => 'client_name'],
            ['table' => 'policies', 'column' => 'policy_no'],
            ['table' => 'users', 'column' => 'name'],
            ['table' => 'users', 'column' => 'email'],
        ];

        foreach ($checks as $check) {
            if (!Schema::hasTable($check['table'])) {
                continue;
            }

            try {
                $count = DB::table($check['table'])
                    ->whereNull($check['column'])
                    ->count();

                if ($count > 0) {
                    $issues[] = [
                        'type' => 'Required Field',
                        'table' => $check['table'],
                        'message' => "Found {$count} record(s) with null value in required field: {$check['column']}",
                        'count' => $count,
                    ];
                }
            } catch (\Exception $e) {
                // Skip if column doesn't exist
            }
        }

        return $issues;
    }

    private function fixIssues(array $issues): int
    {
        $fixed = 0;

        foreach ($issues as $issue) {
            if (!isset($issue['fix'])) {
                continue;
            }

            try {
                DB::statement($issue['fix']);
                $fixed++;
                $this->line("Fixed: {$issue['message']}");
            } catch (\Exception $e) {
                $this->warn("Could not fix: {$issue['message']} - " . $e->getMessage());
            }
        }

        return $fixed;
    }
}
