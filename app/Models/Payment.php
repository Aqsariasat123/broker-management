<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

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
        'paid_on' => 'date',
        'amount' => 'decimal:2',
        'is_encrypted' => 'boolean',
    ];

    public function debitNote(): BelongsTo
    {
        return $this->belongsTo(DebitNote::class);
    }

    public function modeOfPayment(): BelongsTo
    {
        return $this->belongsTo(\App\Models\LookupValue::class, 'mode_of_payment_id');
    }
}

