<?php
// app/Models/LookupValue.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LookupValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'lookup_category_id', 
        'seq', 
        'name', 
        'active', 
        'description', 
        'type', 
        'code'
    ];

    public function category()
    {
        return $this->belongsTo(LookupCategory::class, 'lookup_category_id');
    }

    public function lookupCategory()
    {
        return $this->belongsTo(LookupCategory::class, 'lookup_category_id');
    }
}