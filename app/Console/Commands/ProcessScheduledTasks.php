<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ProcessScheduledTasks extends Command
{
    protected $signature = 'tasks:process {--reminder-hours=24 : Hours before due date to send reminder}';

    protected $description = 'Process scheduled tasks, send reminders, and handle recurring tasks.';

    public function handle(): int
    {
        $reminderHours = (int) $this->option('reminder-hours');
        $this->info("Processing scheduled tasks (reminder window: {$reminderHours} hours)...");

        // 1. Send reminders for tasks due soon
        $reminderCutoff = now()->addHours($reminderHours);
        $tasksDueSoon = Task::where('task_status', '!=', 'Completed')
            ->whereBetween('due_date', [now()->startOfDay(), $reminderCutoff->endOfDay()])
            ->get();

        if ($tasksDueSoon->isNotEmpty()) {
            $recipient = config('mail.from.address');
            if ($recipient) {
                Notification::route('mail', $recipient)
                    ->notify(new TaskReminderNotification($tasksDueSoon));
                $this->info("Sent reminders for " . $tasksDueSoon->count() . " task(s) due soon.");
            }
        }

        // 2. Process recurring tasks
        $recurringTasks = Task::where('repeat', true)
            ->where('task_status', 'Completed')
            ->whereNotNull('frequency')
            ->whereNotNull('rpt_date')
            ->get();

        $created = 0;
        foreach ($recurringTasks as $task) {
            // Check if we should create the next occurrence
            if ($task->rpt_stop_date && $task->rpt_stop_date < now()) {
                continue; // Recurrence has ended
            }

            // Calculate next due date based on frequency
            $nextDueDate = $this->calculateNextDueDate($task->due_date, $task->frequency);
            
            if ($nextDueDate && $nextDueDate <= now()->addDays(7)) {
                // Check if next occurrence already exists
                $existingTask = Task::where('task_id', '!=', $task->task_id)
                    ->where('name', $task->name)
                    ->where('due_date', $nextDueDate)
                    ->first();

                if (!$existingTask) {
                    try {
                        DB::beginTransaction();

                        $newTask = Task::create([
                            'task_id' => Task::generateTaskId(),
                            'category' => $task->category,
                            'description' => $task->description,
                            'name' => $task->name,
                            'contact_no' => $task->contact_no,
                            'due_date' => $nextDueDate,
                            'due_time' => $task->due_time,
                            'date_in' => now(),
                            'assignee' => $task->assignee,
                            'task_status' => 'Not Done',
                            'repeat' => $task->repeat,
                            'frequency' => $task->frequency,
                            'rpt_date' => $task->rpt_date,
                            'rpt_stop_date' => $task->rpt_stop_date,
                            'task_notes' => $task->task_notes,
                        ]);

                        DB::commit();
                        $this->line("Created recurring task: {$newTask->task_id} - {$newTask->name} (Due: {$nextDueDate})");
                        $created++;
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $this->error("Failed to create recurring task for {$task->task_id}: " . $e->getMessage());
                    }
                }
            }
        }

        if ($created > 0) {
            $this->info("Created {$created} recurring task(s).");
        }

        // 3. Mark overdue tasks
        $overdueTasks = Task::where('task_status', '!=', 'Completed')
            ->where('due_date', '<', now()->startOfDay())
            ->get();

        $this->info("Found " . $overdueTasks->count() . " overdue task(s).");

        return Command::SUCCESS;
    }

    private function calculateNextDueDate($lastDueDate, $frequency)
    {
        if (!$lastDueDate || !$frequency) {
            return null;
        }

        $date = Carbon::parse($lastDueDate);

        switch (strtolower($frequency)) {
            case 'daily':
                return $date->addDay();
            case 'weekly':
                return $date->addWeek();
            case 'monthly':
                return $date->addMonth();
            case 'quarterly':
                return $date->addMonths(3);
            case 'yearly':
            case 'annually':
                return $date->addYear();
            default:
                return null;
        }
    }
}
