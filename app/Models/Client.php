<?php
// app/Models/Client.php

namespace App\Models;
use App\Models\LookupValue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'client_type',
        'nin_bcrn',
        'dob_dor',
        'mobile_no',
        'wa',
        'district',
        'occupation',
        'source',
        'status',
        'signed_up',
        'employer',
        'clid',
        'contact_person',
        'income_source',
        'married',
        'spouses_name',
        'children',
        'children_details',
        'alternate_no',
        'email_address',
        'location',
        'island',
        'country',
        'po_box_no',
        'pep',
        'pep_comment',
        'image',
        'salutation',
        'first_name',
        'other_names',
        'surname',
        'passport_no',
        'pic',
        'industry',
        'id_expiry_date',
        'monthly_income',
        'agency',
        'agent',
        'source_name',
        'has_vehicle',
        'has_house',
        'has_business',
        'has_boat',
        'notes',
        'home_no',
        'bday_medium',
        'bday_wish_status',
        'bday_date_done'
    ];

    protected $casts = [
        'dob_dor' => 'date',
        'signed_up' => 'date',
        'id_expiry_date' => 'date',
        'bday_date_done' => 'date',
        'married' => 'boolean',
        'pep' => 'boolean',
        'has_vehicle' => 'boolean',
        'has_house' => 'boolean',
        'has_business' => 'boolean',
        'has_boat' => 'boolean'
    ];

    public function policies(): HasMany
    {
        return $this->hasMany(Policy::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'tied_to', 'clid');
    }

    /**
     * Get the followups for the client.
     */
    public function followups(): HasMany
    {
        return $this->hasMany(Followup::class);
    }

    /**
     * Get the claims for the client.
     */
    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    /**
     * Get the nominees for the client.
     */
    public function nominees(): HasMany
    {
        return $this->hasMany(Nominee::class);
    }


       public function agencies()
    {
        return $this->belongsTo(LookupValue::class, 'agency', 'id');
    }
    public function agents()
    {
        return $this->belongsTo(LookupValue::class, 'agent', 'id');
    }
    public function districts()
    {
        return $this->belongsTo(LookupValue::class, 'district', 'id');
    
    }
        public function salutations()
    {
        return $this->belongsTo(LookupValue::class, 'salutation', 'id'); // 'source' stores LookupValue ID
    }
    public function sources()
    {
        return $this->belongsTo(LookupValue::class, 'source', 'id'); // 'source' stores LookupValue ID
    }
    public function occupations()
    {
        return $this->belongsTo(LookupValue::class, 'occupation', 'id'); // 'occupation' stores LookupValue ID
    }
    
      public function income_sources()
    {
        return $this->belongsTo(LookupValue::class, 'income_source', 'id'); // 'occupation' stores LookupValue ID
    }
      public function islands()
    {
        return $this->belongsTo(LookupValue::class, 'island', 'id'); // 'occupation' stores LookupValue ID
    }   

    public function countries()
    {
        return $this->belongsTo(LookupValue::class, 'country', 'id'); // 'occupation' stores LookupValue ID
    }  
    
}