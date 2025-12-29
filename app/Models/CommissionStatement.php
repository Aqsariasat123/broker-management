<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CommissionStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_note_id',
        'com_stat_id',
        'period_start',
        'period_end',
        'net_commission',
        'tax_withheld',
        'attachment_path',
        'remarks',
        'income_source_id', // make sure this is fillable
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'net_commission' => 'decimal:2',
        'tax_withheld' => 'decimal:2',
    ];

    // CommissionNote relation
    public function commissionNote(): BelongsTo
    {
        return $this->belongsTo(CommissionNote::class);
    }

    // Commissions relation
    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'commission_statement_id');
    }

    // Income relation (1-1)
    public function income(): HasOne
    {
        return $this->hasOne(Income::class, 'id', 'income_source_id');
    }
}
