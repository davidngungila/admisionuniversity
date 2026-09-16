<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'applicant_id',
    'address_type',
    'region_id',
    'district_id',
    'ward_id',
    'street',
    'postal_address',
])]
class ApplicantAddress extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function fullAddress(): string
    {
        $parts = array_filter([
            $this->street,
            $this->ward?->name,
            $this->district?->name,
            $this->region?->name,
        ]);

        return implode(', ', $parts);
    }
}