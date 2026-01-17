<?php

namespace App\Console;

use App\Console\Commands\SendPolicyReminders;
use App\Console\Commands\GenerateDebitNotes;
use App\Console\Commands\SendPaymentFollowUps;
use App\Console\Commands\ProcessScheduledTasks;
use App\Console\Commands\BackupDatabase;
use App\Console\Commands\CheckDataIntegrity;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * @var array<class-string>
     */
    protected $commands = [
        SendPolicyReminders::class,
        GenerateDebitNotes::class,
        SendPaymentFollowUps::class,
        ProcessScheduledTasks::class,
        BackupDatabase::class,
        CheckDataIntegrity::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Policy and payment reminders (daily at 8 AM)
        $schedule->command('policies:send-reminders')->dailyAt('08:00');
        
        // Generate debit notes for upcoming payment plans (daily at 9 AM)
        $schedule->command('debit-notes:generate --days=7')->dailyAt('09:00');
        
        // Send payment follow-ups for overdue payments (daily at 10 AM)
        $schedule->command('payments:send-followups --days-overdue=7')->dailyAt('10:00');
        
        // Process scheduled tasks and send reminders (daily at 11 AM)
        $schedule->command('tasks:process --reminder-hours=24')->dailyAt('11:00');
        
        // Database backup (daily at 2 AM)
        $schedule->command('db:backup --keep=30')->dailyAt('02:00');
        
        // Data integrity check (daily at 3 AM)
        $schedule->command('db:check-integrity')->dailyAt('03:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}

