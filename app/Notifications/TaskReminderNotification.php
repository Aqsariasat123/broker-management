<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class TaskReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Collection $tasks
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Task Reminder: Upcoming Task Deadlines')
            ->greeting('Hi team,')
            ->line('The following tasks are due soon and require attention:');

        $this->tasks->each(function ($task) use (&$mail) {
            $dueDateTime = $task->due_date->format('d-M-Y');
            if ($task->due_time) {
                $dueDateTime .= ' at ' . \Carbon\Carbon::parse($task->due_time)->format('h:i A');
            }

            $mail->line('')
                ->line("**{$task->task_id} - {$task->name}**")
                ->line("- Category: {$task->category}")
                ->line("- Description: {$task->description}")
                ->line("- Due: {$dueDateTime}")
                ->line("- Assignee: {$task->assignee}")
                ->line("- Status: {$task->task_status}");
        });

        $mail->line('')
            ->action('View Tasks', url('/tasks'))
            ->line('Please review and complete these tasks before their deadlines.');

        return $mail;
    }
}
