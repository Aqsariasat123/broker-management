<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class BeneficialOwner extends Model
{
    protected $fillable = [
        'owner_code',
        'client_id',
        'full_name',
        'dob',
        'nin_passport_no',
        'country',
        'expiry_date',
        'status',
        'position',
        'shares',
        'ownership_percentage', // For backward compatibility
        'pep',
        'pep_details',
        'date_added',
        'removed',
        'relationship', // From old migration
        'id_document_path', // From old migration
        'poa_document_path', // From old migration
        'notes' // From old migration
    ];

    protected $casts = [
        'dob' => 'date',
        'expiry_date' => 'date',
        'date_added' => 'date',
        'pep' => 'boolean',
        'removed' => 'boolean',
        'shares' => 'decimal:2'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Accessor for age
    public function getAgeAttribute()
    {
        return $this->dob ? $this->dob->age : null;
    }

    // Accessor to check if expired
    public function getIsExpiredAttribute()
    {
        if (!$this->expiry_date) {
            return false;
        }
        return $this->expiry_date->isPast();
    }

    // Accessor for shares - use shares if available, otherwise ownership_percentage
    public function getSharesAttribute($value)
    {
        // If shares column exists and has a value, use it
        if (isset($this->attributes['shares']) && $this->attributes['shares'] !== null) {
            return $this->attributes['shares'];
        }
        // Fallback to ownership_percentage for backward compatibility
        if (isset($this->attributes['ownership_percentage']) && $this->attributes['ownership_percentage'] !== null) {
            return $this->attributes['ownership_percentage'];
        }
        return $value;
    }
}
