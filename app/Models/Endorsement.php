<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany; // ✅ Correct

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Endorsement extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'endorsement_no',
        'type',
        'effective_date',
        'status',
        'description',
        'endorsement_notes'
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    /**
     * Get the policy that owns the endorsement.
     */
    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'tied_to', 'id');
    }
}
