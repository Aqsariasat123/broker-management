<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statement extends Model
{
    use HasFactory;

    protected $fillable = [
        'statement_no', 'year', 'insurer_id', 'business_category', 'date_received',
        'amount_received', 'mode_of_payment_id', 'remarks'
    ];

    protected $dates = ['date_received'];

    protected $casts = [
        'date_received' => 'date',
        'amount_received' => 'decimal:2',
    ];
    
    public function insurer()
    {
        return $this->belongsTo(\App\Models\LookupValue::class, 'insurer_id');
    }
    public function modeOfPayment()
    {
        return $this->belongsTo(\App\Models\LookupValue::class, 'mode_of_payment_id');
    }
}
