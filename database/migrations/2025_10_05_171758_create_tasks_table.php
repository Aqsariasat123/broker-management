<?php
// database/migrations/2024_01_01_000000_create_tasks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_id')->unique();
            $table->string('category');
            $table->text('description');
            $table->string('name');
            $table->string('contact_no')->nullable();
            $table->date('due_date');
            $table->time('due_time')->nullable();
            $table->date('date_in')->nullable();
            $table->string('assignee');
            $table->enum('task_status', ['Not Done', 'In Progress', 'Completed'])->default('Not Done');
            $table->date('date_done')->nullable();
            $table->boolean('repeat')->default(false);
            $table->string('frequency')->nullable();
            $table->date('rpt_date')->nullable();
            $table->date('rpt_stop_date')->nullable();
            $table->text('task_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}