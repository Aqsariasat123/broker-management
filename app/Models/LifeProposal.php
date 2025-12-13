<?php
// app/Models/LifeProposal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LifeProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposers_name',
        'insurer',
        'policy_plan',
        'sum_assured',
        'term',
        'add_ons',
        'offer_date',
        'premium',
        'frequency',
        'stage',
        'date',
        'age',
        'status',
        'source_of_payment',
        'mcr',
        'doctor',
        'date_sent',
        'date_completed',
        'notes',
        'agency',
        'prid',
        'class',
        'is_submitted'
    ];

    protected $casts = [
        'sum_assured' => 'decimal:2',
        'premium' => 'decimal:2',
        'offer_date' => 'date',
        'date' => 'date',
        'date_sent' => 'date',
        'date_completed' => 'date',
        'is_submitted' => 'boolean'
    ];

    public function hasExpired()
    {
        if (!$this->offer_date || $this->is_submitted) {
            return false;
        }
        return $this->offer_date < now()->startOfDay();
    }

    public function hasExpiring()
    {
        if (!$this->offer_date || $this->is_submitted || $this->hasExpired()) {
            return false;
        }
        $today = now()->startOfDay();
        $offerDate = \Carbon\Carbon::parse($this->offer_date)->startOfDay();
        $daysUntilOffer = $today->diffInDays($offerDate, false);
        return $daysUntilOffer >= 0 && $daysUntilOffer <= 7;
    }
}