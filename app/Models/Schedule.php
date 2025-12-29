<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'schedule_no',
        'issued_on',
        'effective_from',
        'effective_to',
        'status',
        'debit_note_path',
        'receipt_path',
        'policy_schedule_path',
        'renewal_notice_path',
        'payment_agreement_path',
        'notes',
    ];

    protected $casts = [
        'issued_on' => 'date',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];
   public function policy()
    {
        return $this->belongsTo(
            Policy::class,
            'policy_id'
        );
    }

    public function paymentPlans(): HasMany
    {
        return $this->hasMany(PaymentPlan::class);
    }

    /**
     * Get the commission notes for the schedule.
     */
    public function commissionNotes(): HasMany
    {
        return $this->hasMany(CommissionNote::class);
    }
}

