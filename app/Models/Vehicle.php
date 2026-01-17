<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'regn_no',
        'make',
        'model',
        'type',
        'useage',
        'year',
        'value',
        'policy_id',
        'engine_type',
        'cc',
        'engine_no',
        'chassis_no',
        'from',
        'to',
        'notes',
        'vehicle_seats',
        'vehicle_color'
    ];

    protected $with = [
        'makeLookup',
        'typeLookup',
        'useageLookup',
        'vehicleColorLookup',
        'engineTypeLookup',
        'policy.client'
    ];

    protected $casts = [
        'from'  => 'date',
        'to'    => 'date',
        'value' => 'decimal:2',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }

    public function makeLookup(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'make');
    }

    public function typeLookup(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'type');
    }

    public function useageLookup(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'useage');
    }

    public function vehicleColorLookup(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'vehicle_color');
    }

    public function engineTypeLookup(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'engine_type');
    }
}
