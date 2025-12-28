<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RenewalNotice extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'rnid',
        'notice_date',
        'status',
        'delivery_method',
        'document_path',
        'remarks',
    ];

    protected $casts = [
        'notice_date' => 'date',
    ];

     public static function generateRNID(): string
    {
        $latest = self::orderBy('id', 'desc')->first();
        if (!$latest) {
            return 'RN000001';
        }

        // Extract number from latest policy number
        $number = 1;
        if (preg_match('/POL(\d+)/', $latest->rnid, $matches)) {
            $number = (int)$matches[1] + 1;
        } else {
            // If format doesn't match, try to extract any number
            $extracted = (int)filter_var($latest->rnid, FILTER_SANITIZE_NUMBER_INT);
            if ($extracted > 0) {
                $number = $extracted + 1;
            }
        }
        
        return 'RN' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
    /**
     * Get the policy that owns the renewal notice.
     */
    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }
}
