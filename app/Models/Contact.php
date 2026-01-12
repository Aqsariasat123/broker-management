<?php
// app/Models/Contact.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_name',
        'contact_no',
        'wa',
        'type',
        'occupation',
        'employer',
        'acquired',
        'source',
        'status',
        'rank',
        'first_contact',
        'next_follow_up',
        'coid',
        'dob',
        'salutation',
        'source_name',
        'agency',
        'agent',
        'address',
        'location',
        'email_address',
        'contact_id',
        'savings_budget',
        'married',
        'children',
        'children_details',
        'vehicle',
        'house',
        'business',
        'other'
    ];

    protected $casts = [
        'acquired' => 'date',
        'first_contact' => 'date',
        'next_follow_up' => 'date',
        'dob' => 'date',
        'married' => 'boolean',
        'savings_budget' => 'decimal:2'
    ];

    public function hasExpired()
    {
        if (!$this->next_follow_up || $this->status === 'Archived') {
            return false;
        }
        return $this->next_follow_up < now()->startOfDay();
    }

    public function hasExpiring()
    {
        if (!$this->next_follow_up || $this->status === 'Archived' || $this->hasExpired()) {
            return false;
        }
        $today = now()->startOfDay();
        $followUpDate = \Carbon\Carbon::parse($this->next_follow_up)->startOfDay();
        $daysUntilFollowUp = $today->diffInDays($followUpDate, false);
        return $daysUntilFollowUp >= 0 && $daysUntilFollowUp <= 7;
    }

    public function getAge()
    {
        if (!$this->dob) {
            return null;
        }
        return \Carbon\Carbon::parse($this->dob)->age;
    }

    /**
     * Get the life proposals for the contact.
     */
    public function lifeProposals()
    {
        return $this->hasMany(LifeProposal::class);
    }

    /**
     * Get the followups for the contact.
     */
    public function followups()
    {
        return $this->hasMany(Followup::class);
    }
 /**
     * Lookup type/category
     */
   public function contact_types()
    {
        return $this->belongsTo(LookupValue::class, 'type', 'id'); // 'type' stores LookupValue ID
     }

    /**
     * Lookup source
     */
    public function source_value()
    {
        return $this->belongsTo(LookupValue::class, 'source', 'id'); // 'source' stores LookupValue ID
    }

    public function salutations()
    {
        return $this->belongsTo(LookupValue::class, 'salutation', 'id'); // 'source' stores LookupValue ID
    }

    

    /**
     * Assigned agent
     */
    public function agent_user()
    {
        return $this->belongsTo(User::class, 'agent', 'id');
    }
    public function rank()
    {
        return $this->belongsTo(LookupValue::class, 'rank', 'id');
    }
    public function statusRelation()
    {
        return $this->belongsTo(LookupValue::class, 'status', 'id');
    }

    
    /**
     * Assigned agency
     */
    public function agency_user()
    {
        return $this->belongsTo(User::class, 'agency', 'id'); // if agency stores user ID
    }

}