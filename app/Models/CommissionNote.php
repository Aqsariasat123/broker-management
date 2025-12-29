<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommissionNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'com_note_id',
        'issued_on',
        'total_premium',
        'expected_commission',
        'attachment_path',
        'remarks',
    ];

    protected $casts = [
        'issued_on' => 'date',
        'total_premium' => 'decimal:2',
        'expected_commission' => 'decimal:2',
    ];

    /**
     * Get the schedule that owns the commission note.
     */
       public function schedule()
    {
        return $this->hasOne(
            Schedule::class,
            'id',
            'schedule_id'
        );
    }

    /**
     * Get the commissions for the commission note.
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    /**
     * Get the commission statements for the commission note.
     */
    public function commissionStatements(): HasMany
    {
        return $this->hasMany(CommissionStatement::class);
    }
}
