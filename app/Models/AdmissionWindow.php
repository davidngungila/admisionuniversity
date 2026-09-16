<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'academic_year_id',
    'admission_level_id',
    'application_round_id',
    'applicant_category',
    'opens_at',
    'closes_at',
    'timezone',
    'application_fee',
    'currency',
    'payment_reference_format',
    'status',
])]
class AdmissionWindow extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected function casts(): array
    {
        return [
            'opens_at' => 'datetime',
            'closes_at' => 'datetime',
            'application_fee' => 'decimal:2',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function admissionLevel(): BelongsTo
    {
        return $this->belongsTo(AdmissionLevel::class);
    }

    public function applicationRound(): BelongsTo
    {
        return $this->belongsTo(ApplicationRound::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function isOpen(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE || ! $this->academicYear->is_active) {
            return false;
        }

        $now = now();
        return $now->gte($this->opens_at) && $now->lte($this->closes_at);
    }

    public function isUpcoming(): bool
    {
        return $this->status === self::STATUS_ACTIVE && now()->lt($this->opens_at);
    }

    public function isClosed(): bool
    {
        return $this->status !== self::STATUS_ACTIVE || now()->gt($this->closes_at);
    }

    /** OPEN | CLOSING SOON | CLOSED | UPCOMING */
    public function statusLabel(): string
    {
        if ($this->isClosed()) {
            return 'CLOSED';
        }
        if ($this->isUpcoming()) {
            return 'UPCOMING';
        }
        if (now()->diffInHours($this->closes_at) <= 72) {
            return 'CLOSING SOON';
        }

        return 'OPEN';
    }

    public function feeLabel(): string
    {
        $fee = (float) $this->application_fee;

        return $fee <= 0 ? 'FREE' : $this->currency.' '.number_format($fee);
    }
}