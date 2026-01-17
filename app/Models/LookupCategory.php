<?php
// app/Models/LookupCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LookupCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'active'];

    public function values()
    {
        return $this->hasMany(LookupValue::class)->orderBy('seq');
    }
}