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
        // 'class',
        'is_submitted',
        'sex',
        'anb',
        'riders',
        'rider_premiums',
        'annual_premium',
        'base_premium',
        'admin_fee',
        'total_premium',
        'medical_examination_required',
        'clinic',
        'date_referred',
        'exam_notes',
        'policy_no',
        'loading_premium',
        'start_date',
        'maturity_date',
        'method_of_payment',
        'source_name'
    ];

    protected $casts = [
        'sum_assured' => 'decimal:2',
        'premium' => 'decimal:2',
        'annual_premium' => 'decimal:2',
        'base_premium' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'total_premium' => 'decimal:2',
        'loading_premium' => 'decimal:2',
        'offer_date' => 'date',
        'date' => 'date',
        'date_sent' => 'date',
        'date_completed' => 'date',
        'date_referred' => 'date',
        'start_date' => 'date',
        'maturity_date' => 'date',
        'is_submitted' => 'boolean',
        'medical_examination_required' => 'boolean',
        'riders' => 'array',
        'rider_premiums' => 'array'
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

    /**
     * Get the contact that owns the life proposal.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the medical record for the life proposal.
     */
    public function medical()
    {
        return $this->hasOne(Medical::class);
    }

    /**
     * Get the followups for the life proposal.
     */
    public function followups()
    {
        return $this->hasMany(Followup::class);
    }
}