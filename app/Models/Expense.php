<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'expense_code',
        'payee',
        'date_paid',
        'amount_paid',
        'description',
        'category_id',
        'mode_of_payment',
        'mode_of_payment_id',
        'receipt_no',
        'expense_notes',
        'notes',
        'attachment_path'
    ];

    protected $casts = [
        'date_paid' => 'date',
        'amount_paid' => 'decimal:2'
    ];

    public function expenseCategory()
    {
        return $this->belongsTo(\App\Models\LookupValue::class, 'category_id');
    }

    public function modeOfPayment()
    {
        return $this->belongsTo(\App\Models\LookupValue::class, 'mode_of_payment_id');
    }
}