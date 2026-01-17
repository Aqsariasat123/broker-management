<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class PaymentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'installment_label',
        'due_date',
        'amount',
        'frequency',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function debitNotes(): HasMany
    {
        return $this->hasMany(DebitNote::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, DebitNote::class);
    }

    public function scopeDueWithin($query, int $days)
    {
        return $query->whereBetween('due_date', [now()->startOfDay(), now()->addDays($days)]);
    }
       public function lookuFrequency(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'frequency');
    }
}


