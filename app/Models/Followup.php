<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Followup extends Model
{
    use HasFactory;

    protected $fillable = [
        'follow_up_code',
        'contact_id',
        'client_id',
        'life_proposal_id',
        'user_id',
        'follow_up_date',
        'channel',
        'status',
        'summary',
        'next_action',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'status' => 'string', // or enum in Laravel 10+
        'channel' => 'string',
    ];

    protected $attributes = [
    'status' => 'Open',
    'channel' => 'System',
    ];
    /**
     * Get the contact that owns the followup.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the client that owns the followup.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the life proposal that owns the followup.
     */
    public function lifeProposal(): BelongsTo
    {
        return $this->belongsTo(LifeProposal::class);
    }

    /**
     * Get the user that owns the followup.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
