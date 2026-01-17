<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DebitNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_plan_id',
        'debit_note_no',
        'issued_on',
        'amount',
        'status',
        'document_path',
        'is_encrypted',
    ];

    protected $casts = [
        'issued_on' => 'date',
        'amount' => 'decimal:2',
        'is_encrypted' => 'boolean',
    ];

    public function paymentPlan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'debit_note_id');
    }

    public static function generateDebitNoteNo(): string
    {
        $latest = self::orderBy('id', 'desc')->first();
        if (!$latest) {
            return 'DN000001';
        }
        
        // Extract number from latest debit note number
        $number = 1;
        if (preg_match('/DN(\d+)/', $latest->debit_note_no, $matches)) {
            $number = (int)$matches[1] + 1;
        } else {
            // If format doesn't match, try to extract any number
            $extracted = (int)filter_var($latest->debit_note_no, FILTER_SANITIZE_NUMBER_INT);
            if ($extracted > 0) {
                $number = $extracted + 1;
            }
        }
        
        return 'DN' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}

