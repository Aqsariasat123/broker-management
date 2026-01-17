<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_id', 'tied_to', 'name', 'group', 'type', 'format', 'date_added', 'year', 'notes', 'file_path'
    ];

    protected $casts = [
        'date_added' => 'date',
    ];
}
