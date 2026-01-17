<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medical extends Model
{
    use HasFactory;

    protected $fillable = [
        'life_proposal_id',
        'medical_code',
        'status_id',
        'medical_type_id',
        'clinic',
        'provider',
        'ordered_on',
        'completed_on',
        'status',
        'results_path',
        'notes',
    ];

    protected $casts = [
        'ordered_on' => 'date',
        'completed_on' => 'date',
    ];

    /**
     * Get the life proposal that owns the medical.
     */
    public function lifeProposal(): BelongsTo
    {
        return $this->belongsTo(LifeProposal::class);
    }

    public function medicalType()
    {
        return $this->belongsTo(LookupValue::class, 'medical_type_id');
    }

       public function clinic()
    {
        return $this->belongsTo(LookupValue::class, 'clinic');
    }
}
