<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'application_id',
    'payment_reference',
    'amount',
    'currency',
    'status',
    'payment_method',
    'control_number',
    'phone_number',
    'transaction_id',
    'amount_paid',
    'paid_by_name',
    'paid_at',
    'verified_by',
    'verified_at',
    'receipt_number',
    'remarks',
])]
class Payment extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public const STATUS_PENDING   = 'PENDING';
    public const STATUS_CONFIRMED = 'CONFIRMED';
    public const STATUS_FAILED    = 'FAILED';
    public const STATUS_FREE      = 'FREE';

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'paid_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public static function generateReference(): string
    {
        return 'PAY-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(4)));
    }
}