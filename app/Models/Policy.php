<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'policy_no',
        'policy_code',
        'insurer_id',
        'policy_class_id',
        'policy_plan_id',
        'sum_insured',
        'start_date',
        'end_date',
        'insured',
        'insured_item',
        'policy_status_id',
        'date_registered',
        'renewable',
        'business_type_id',
        'term',
        'term_unit',
        'base_premium',
        'premium',
        'wsc',
        'lou',
        'pa',
        'frequency_id',
        'pay_plan_lookup_id',
        'agency_id',
        'agent',
        'channel_id',
        'cancelled_date',
        'last_endorsement',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'date_registered' => 'date',
        'cancelled_date' => 'date',
        'sum_insured' => 'decimal:2',
        'base_premium' => 'decimal:2',
        'premium' => 'decimal:2',
        'wsc' => 'decimal:2',
        'lou' => 'decimal:2',
        'pa' => 'decimal:2',
        'renewable' => 'boolean',
    ];


   public static function generatePolicyNo(): string
    {
        $latest = self::orderBy('id', 'desc')->first();
        if (!$latest) {
            return 'POL000001';
        }

        // Extract number from latest policy number
        $number = 1;
        if (preg_match('/POL(\d+)/', $latest->policy_no, $matches)) {
            $number = (int)$matches[1] + 1;
        } else {
            // If format doesn't match, try to extract any number
            $extracted = (int)filter_var($latest->policy_no, FILTER_SANITIZE_NUMBER_INT);
            if ($extracted > 0) {
                $number = $extracted + 1;
            }
        }
        
        return 'POL' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function paymentPlans(): HasManyThrough
    {
        return $this->hasManyThrough(PaymentPlan::class, Schedule::class);
    }

    public function isDueForRenewal(): bool
    {
        if (!$this->end_date) {
            return false;
        }

        $daysUntilRenewal = now()->diffInDays($this->end_date, false);
        return $daysUntilRenewal <= 30 && $daysUntilRenewal >= 0;
    }

    public function isExpired(): bool
    {
        if (!$this->end_date) {
            return false;
        }

        return $this->end_date->isPast();
    }

    public function insurer(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'insurer_id');
    }

    public function policyClass(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'policy_class_id');
    }

    public function policyPlan(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'policy_plan_id');
    }

    public function policyStatus(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'policy_status_id');
    }

    public function frequency(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'frequency_id');
    }

    public function businessType(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'business_type_id');
    }

    public function payPlan(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'pay_plan_lookup_id');
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'agency_id');
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'channel_id');
    }

    /**
     * Get the vehicles for the policy.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Get the claims for the policy.
     */
    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    /**
     * Get the nominees for the policy.
     */
    public function nominees(): HasMany
    {
        return $this->hasMany(Nominee::class);
    }

    /**
     * Get the endorsements for the policy.
     */
    public function endorsements(): HasMany
    {
        return $this->hasMany(Endorsement::class);
    }

    /**
     * Get the renewal notices for the policy.
     */
    public function renewalNotices(): HasMany
    {
        return $this->hasMany(RenewalNotice::class);
    }

    // Note: Documents are tied using 'tied_to' field which can be policy_code or policy_no
    // We can't use a standard relationship here, so we'll load documents manually in controllers

    // Accessor methods to safely get relationship names
    public function getInsurerNameAttribute(): ?string
    {
        if (!$this->insurer_id) {
            return null;
        }
        try {
            if ($this->relationLoaded('insurer') && $this->insurer) {
                return (string) $this->insurer->name;
            }
            // Lazy load if not already loaded
            $insurer = $this->insurer;
            if ($insurer && is_object($insurer) && isset($insurer->name)) {
                return (string) $insurer->name;
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        return null;
    }

    public function getPolicyClassNameAttribute(): ?string
    {
        if (!$this->policy_class_id) {
            return null;
        }
        try {
            if ($this->relationLoaded('policyClass') && $this->policyClass) {
                return (string) $this->policyClass->name;
            }
            $policyClass = $this->policyClass;
            if ($policyClass && is_object($policyClass) && isset($policyClass->name)) {
                return (string) $policyClass->name;
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        return null;
    }

    public function getPolicyPlanNameAttribute(): ?string
    {
        if (!$this->policy_plan_id) {
            return null;
        }
        try {
            if ($this->relationLoaded('policyPlan') && $this->policyPlan) {
                return (string) $this->policyPlan->name;
            }
            $policyPlan = $this->policyPlan;
            if ($policyPlan && is_object($policyPlan) && isset($policyPlan->name)) {
                return (string) $policyPlan->name;
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        return null;
    }

    public function getPolicyStatusNameAttribute(): ?string
    {
        try {
            $policyStatus = $this->policyStatus;
            if ($policyStatus && is_object($policyStatus) && property_exists($policyStatus, 'name')) {
                $name = $policyStatus->name;
                return is_scalar($name) ? (string) $name : null;
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        return null;
    }

    public function getFrequencyNameAttribute(): ?string
    {
        try {
            $frequency = $this->frequency;
            if ($frequency && is_object($frequency) && property_exists($frequency, 'name')) {
                $name = $frequency->name;
                return is_scalar($name) ? (string) $name : null;
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        return null;
    }

    public function getBusinessTypeNameAttribute(): ?string
    {
        try {
            $businessType = $this->businessType;
            if ($businessType && is_object($businessType) && property_exists($businessType, 'name')) {
                $name = $businessType->name;
                return is_scalar($name) ? (string) $name : null;
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        return null;
    }

    public function getPayPlanNameAttribute(): ?string
    {
        try {
            $payPlan = $this->payPlan;
            if ($payPlan && is_object($payPlan) && property_exists($payPlan, 'name')) {
                $name = $payPlan->name;
                return is_scalar($name) ? (string) $name : null;
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        return null;
    }

    public function getAgencyNameAttribute(): ?string
    {
        try {
            $agency = $this->agency;
            if ($agency && is_object($agency) && property_exists($agency, 'name')) {
                $name = $agency->name;
                return is_scalar($name) ? (string) $name : null;
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        return null;
    }

    public function getClientNameAttribute(): ?string
    {
        if (!$this->client_id) {
            return null;
        }
        try {
            if ($this->relationLoaded('client') && $this->client) {
                return (string) $this->client->client_name;
            }
            $client = $this->client;
            if ($client && is_object($client) && isset($client->client_name)) {
                return (string) $client->client_name;
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        return null;
    }

    public function getChannelNameAttribute(): ?string
    {
        try {
            $channel = $this->channel;
            if ($channel && is_object($channel) && property_exists($channel, 'name')) {
                $name = $channel->name;
                return is_scalar($name) ? (string) $name : null;
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        return null;
    }

    public function commissionNotes(): HasManyThrough
    {
        return $this->hasManyThrough(
            CommissionNote::class,
            Schedule::class
        );
    }
    public function commissions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Commission::class,
            CommissionNote::class,
            'schedule_id',          // FK on commission_notes
            'commission_note_id',   // FK on commissions
            'id',                   // local key on policies
            'id'                    // local key on commission_notes
        );
    }
        public function getTotalCommissionAttribute(): float
        {
            return $this->commissionNotes()->sum('expected_commission');
        }

        public function getOutstandingCommissionAttribute(): float
        {
            return $this->commissions()
                ->whereNull('date_rcvd')
                ->sum('amount_due');
        }
}