<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'income_id', 'income_code', 'commission_statement_id', 'income_source_id', 'date_received', 'amount_received', 'description',
        'category_id', 'mode_of_payment_id', 'statement_no', 'income_notes', 'notes'
    ];

    protected $casts = [
        'date_received' => 'date',
        'amount_received' => 'decimal:2'
    ];

    // Accessor for date
    public function getDateAttribute()
    {
        return $this->date_received ?? null;
    }

    public function incomeSource()
    {
        return $this->belongsTo(\App\Models\LookupValue::class, 'income_source_id');
    }

    public function modeOfPayment()
    {
        return $this->belongsTo(\App\Models\LookupValue::class, 'mode_of_payment_id');
    }

    public function incomeCategory()
    {
        return $this->belongsTo(\App\Models\LookupValue::class, 'category_id');
    }

    /**
     * Get the commission statement that owns the income.
     */
 public function commissionStatement()
    {
        return $this->hasOne(
            CommissionStatement::class,
            'id',
            'commission_statement_id'
        );
    }
}
