<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nominee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nominee_code',
        'policy_id',
        'client_id',
        'full_name',
        'relationship',
        'share_percentage',
        'date_of_birth',
        'date_removed',
        'id_document_path',
        'notes',
        'nin_passport_no'
    ];

    protected $dates = ['date_of_birth', 'date_removed'];

    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

