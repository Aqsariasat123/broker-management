<?php
// database/seeders/TaskSeeder.php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run()
    {
        $tasks = [
            [
                'task_id' => 'TK24043',
                'category' => 'Payment',
                'description' => 'P.O. Box Rental',
                'name' => 'Seychelles Postal Services',
                'contact_no' => '2765937',
                'due_date' => Carbon::create(2025, 10, 18),
                'due_time' => null,
                'date_in' => null,
                'assignee' => 'Mandy',
                'task_status' => 'Not Done',
                'date_done' => null,
                'repeat' => true,
                'frequency' => 'Annually',
                'rpt_date' => Carbon::create(2025, 1, 1),
                'rpt_stop_date' => Carbon::create(2027, 12, 31),
                'task_notes' => null
            ],
            [
                'task_id' => 'TK24044',
                'category' => 'Report',
                'description' => 'Beneficial Owner Report',
                'name' => 'FIU',
                'contact_no' => '4282828',
                'due_date' => Carbon::create(2025, 10, 17),
                'due_time' => null,
                'date_in' => null,
                'assignee' => 'Mandy',
                'task_status' => 'Not Done',
                'date_done' => null,
                'repeat' => true,
                'frequency' => 'Bi-Annually',
                'rpt_date' => Carbon::create(2025, 1, 15),
                'rpt_stop_date' => Carbon::create(2026, 12, 31),
                'task_notes' => null
            ]
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}