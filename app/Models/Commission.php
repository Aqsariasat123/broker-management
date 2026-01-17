<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_note_id', 'commission_statement_id', 'insurer_id', 'grouping', 'basic_premium', 'rate', 'amount_due',
        'payment_status_id', 'amount_received', 'date_received', 'statement_no', 'mode_of_payment_id',
        'variance','variance_reason', 'reason', 'date_due', 'cnid','commission_code'
    ];

    protected $dates = ['date_received', 'date_due'];
    protected $casts = [
        'date_received' => 'datetime',
        'date_due'  => 'datetime',
    ];
    public function insurer()
    {
        return $this->belongsTo(\App\Models\LookupValue::class, 'insurer_id');
    }
    public function paymentStatus()
    {
        return $this->belongsTo(\App\Models\LookupValue::class, 'payment_status_id');
    }
    public function modeOfPayment()
    {
        return $this->belongsTo(\App\Models\LookupValue::class, 'mode_of_payment_id');
    }

    /**
     * Get the commission note that owns the commission.
     */
   public function commissionNote()
    {
        return $this->belongsTo(
            CommissionNote::class,
            'commission_note_id'
        );
    }

    /**
     * Get the commission statement that owns the commission.
     */
    public function commissionStatement(): BelongsTo
    {
        return $this->belongsTo(CommissionStatement::class);
    }
}
