<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // ← Keep this if using deleted_at column

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes; // ← OPTION 1: Keep this IF you added deleted_at column (RECOMMENDED)
    // use HasFactory; // ← OPTION 2: Comment out SoftDeletes IF you don't want soft deletes
    
    protected $fillable = [
        'debit_note_id',
        'payment_reference',
        'paid_on',
        'amount',
        'mode_of_payment_id',
        'receipt_path',
        'is_encrypted',
        'notes',
    ];

    protected $casts = [
        'paid_on' => 'datetime',
        'amount' => 'decimal:2',
        'is_encrypted' => 'boolean',
    ];

    /**
     * RELATIONSHIPS
     */
    
    // Payment belongs to DebitNote
    public function debitNote()
    {
        return $this->belongsTo(\App\Models\DebitNote::class, 'debit_note_id');
    }

    // Payment has mode of payment (lookup)
    public function modeOfPayment()
    {
        return $this->belongsTo(\App\Models\LookupValue::class, 'mode_of_payment_id');
    }

    /**
     * HELPER METHODS
     */
    
    // Get policy through relationships
    public function getPolicy()
    {
        return $this->debitNote
            ? ($this->debitNote->paymentPlan
                ? ($this->debitNote->paymentPlan->schedule
                    ? $this->debitNote->paymentPlan->schedule->policy
                    : null)
                : null)
            : null;
    }

    // Get client through relationships
    public function getClient()
    {
        $policy = $this->getPolicy();
        return $policy ? $policy->client : null;
    }

    // Format amount for display
    public function getFormattedAmountAttribute()
    {
        return 'Rs ' . number_format($this->amount, 2);
    }

    /**
     * SCOPES
     */
    
    // Get payments for specific client
    public function scopeForClient($query, $clientId)
    {
        return $query->whereHas('debitNote.paymentPlan.schedule.policy', function($q) use ($clientId) {
            $q->where('client_id', $clientId);
        });
    }

    // Get payments for specific policy
    public function scopeForPolicy($query, $policyId)
    {
        return $query->whereHas('debitNote.paymentPlan.schedule.policy', function($q) use ($policyId) {
            $q->where('id', $policyId);
        });
    }

    // Get payments by date range
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('paid_on', [$from, $to]);
    }

    /**
     * BOOT METHOD - Auto-generate payment reference
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($payment) {
            // Auto-generate payment reference if not provided
            if (!$payment->payment_reference) {
                $latest = self::orderBy('id', 'desc')->first();
                $nextId = $latest ? $latest->id + 1 : 1;
                $payment->payment_reference = 'PAY-' . date('Y') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}