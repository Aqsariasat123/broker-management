<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LifeProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposers_name',
        'contact_id',
        'insurer_id',
        'policy_plan_id',
        'salutation_id',
        'sum_assured',
        'term',
        'add_ons',
        'offer_date',
        'premium',
        'frequency_id',
        'proposal_stage_id',
        'age',
        'status_id',
        'source_of_payment_id',
        'mcr',
        'policy_no',
        'loading_premium',
        'start_date',
        'maturity_date',
        'method_of_payment',
        'agency',
        'prid',
        'is_submitted',
        'sex',
        'anb',
        'riders',
        'rider_premiums',
        'annual_premium',
        'base_premium',
        'admin_fee',
        'total_premium',
        'medical_examination_required',
        'source_name'
    ];

    protected $casts = [
        'sum_assured' => 'decimal:2',
        'premium' => 'decimal:2',
        'annual_premium' => 'decimal:2',
        'base_premium' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'total_premium' => 'decimal:2',
        'loading_premium' => 'decimal:2',

        'offer_date' => 'date',
        'start_date' => 'date',
        'maturity_date' => 'date',

        'is_submitted' => 'boolean',
        'medical_examination_required' => 'boolean',

        'riders' => 'array',
        'rider_premiums' => 'array',
    ];

    /* ------------------------- */
    /* Relationships */
    /* ------------------------- */

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

  
    public function insurer()
    {
        return $this->belongsTo(LookupValue::class, 'insurer_id');
    }

    public function policyPlan()
    {
        return $this->belongsTo(LookupValue::class, 'policy_plan_id');
    }

    public function frequency()
    {
        return $this->belongsTo(LookupValue::class, 'frequency_id');
    }

   public function agencies()
    {
        return $this->belongsTo(LookupValue::class, 'agency');
    }

    public function stage()
    {
        return $this->belongsTo(LookupValue::class, 'proposal_stage_id');
    }

    public function status()
    {
        return $this->belongsTo(LookupValue::class, 'status_id');
    }

    public function sourceOfPayment()
    {
        return $this->belongsTo(LookupValue::class, 'source_of_payment_id');
    }

    public function proposalClass()
    {
        return $this->belongsTo(LookupValue::class, 'class_id');
    }

    public function medical()
    {
        return $this->hasOne(Medical::class);
    }

    public function followups()
    {
        return $this->hasOne(Followup::class);
    }

    /* ------------------------- */
    /* Business Logic */
    /* ------------------------- */

    public function hasExpired()
    {
        if (!$this->offer_date || $this->is_submitted) {
            return false;
        }

        return $this->offer_date < now()->startOfDay();
    }

    public function hasExpiring()
    {
        if (!$this->offer_date || $this->is_submitted || $this->hasExpired()) {
            return false;
        }

        $days = now()->startOfDay()->diffInDays($this->offer_date, false);

        return $days >= 0 && $days <= 7;
    }
}
