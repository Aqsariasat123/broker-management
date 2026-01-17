<?php
// app/Models/Task.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'category',
        'item',
        'description',
        'name',
        'contact_no',
        'due_date',
        'due_time',
        'date_in',
        'assignee',
        'task_status',
        'date_done',
        'repeat',
        'frequency',
        'rpt_date',
        'rpt_stop_date',
        'task_notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'date_done' => 'date',
        'rpt_date' => 'date',
        'rpt_stop_date' => 'date',
        'repeat' => 'boolean',
    ];

    public function isOverdue()
    {
        return $this->due_date < now()->format('Y-m-d') && $this->task_status !== 'Completed';
    }

    public function isExpiringSoon($days = 7)
    {
        if ($this->task_status === 'Completed' || !$this->due_date) {
            return false;
        }
        
        $today = now()->startOfDay();
        $dueDate = \Carbon\Carbon::parse($this->due_date)->startOfDay();
        $daysUntilDue = $today->diffInDays($dueDate, false);
        
        // Return true if due date is within the next $days days and not overdue
        return $daysUntilDue >= 0 && $daysUntilDue <= $days;
    }

    public function getDueInDays()
    {
        if (!$this->due_date || $this->task_status === 'Completed') {
            return null;
        }
        
        $today = now()->startOfDay();
        $dueDate = \Carbon\Carbon::parse($this->due_date)->startOfDay();
        return $today->diffInDays($dueDate, false);
    }

    public static function generateTaskId()
    {
        $latest = self::where('task_id', 'like', 'TK%')->orderBy('id', 'desc')->first();
        if (!$latest) {
            return 'TK24001';
        }
        
        $number = intval(substr($latest->task_id, 2)) + 1;
        return 'TK' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

     public function categoryValues()
    {
        return $this->belongsTo(LookupCategory::class, 'category', 'id');
    }

    // Assignee relationship
    public function assigneeUser()
    {
        return $this->belongsTo(User::class, 'assignee', 'id');
    }

    // Contact relationship
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'name', 'id'); // if `name` stores contact id
    }

    // Client relationship
    public function client()
    {
        return $this->belongsTo(Client::class, 'name', 'id'); // if `name` stores client id
    }
}
