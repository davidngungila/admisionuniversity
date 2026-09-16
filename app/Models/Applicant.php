<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'applicant_number',
    'first_name',
    'middle_name',
    'last_name',
    'date_of_birth',
    'gender',
    'citizenship_id',
    'phone',
    'email',
    'exam_index_number',
    'disability_status',
    'disability_type',
    'nida_number',
    'marital_status',
    'place_of_birth',
    'photo',
])]
class Applicant extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'disability_status' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function citizenship(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'citizenship_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(ApplicantAddress::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function currentAddress(): ?ApplicantAddress
    {
        return $this->addresses()->where('address_type', 'Current')->latest()->first()
            ?? $this->addresses()->latest()->first();
    }

    public function fullName(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }
}