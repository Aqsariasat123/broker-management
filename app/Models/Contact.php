<?php
// app/Models/Contact.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_name',
        'contact_no',
        'wa',
        'type',
        'occupation',
        'employer',
        'acquired',
        'source',
        'status',
        'rank',
        'first_contact',
        'next_follow_up',
        'coid',
        'dob',
        'salutation',
        'source_name',
        'agency',
        'agent',
        'address',
        'location',
        'email_address',
        'contact_id',
        'savings_budget',
        'married',
        'children',
        'children_details',
        'vehicle',
        'house',
        'business',
        'other'
    ];

    protected $casts = [
        'acquired' => 'date',
        'first_contact' => 'date',
        'next_follow_up' => 'date',
        'dob' => 'date',
        'married' => 'boolean',
        'savings_budget' => 'decimal:2'
    ];

    public function hasExpired()
    {
        if (!$this->next_follow_up || $this->status === 'Archived') {
            return false;
        }
        return $this->next_follow_up < now()->startOfDay();
    }

    public function hasExpiring()
    {
        if (!$this->next_follow_up || $this->status === 'Archived' || $this->hasExpired()) {
            return false;
        }
        $today = now()->startOfDay();
        $followUpDate = \Carbon\Carbon::parse($this->next_follow_up)->startOfDay();
        $daysUntilFollowUp = $today->diffInDays($followUpDate, false);
        return $daysUntilFollowUp >= 0 && $daysUntilFollowUp <= 7;
    }

    public function getAge()
    {
        if (!$this->dob) {
            return null;
        }
        return \Carbon\Carbon::parse($this->dob)->age;
    }
}