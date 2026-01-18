<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_id', 'policy_id', 'policy_no', 'client_id', 'loss_date', 'claim_date', 'claim_amount',
        'claim_summary', 'claim_stage', 'status', 'close_date', 'paid_amount', 'settlment_notes', 'comments'
    ];

    protected $dates = ['loss_date', 'claim_date', 'close_date'];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class, 'policy_no', 'policy_no');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
