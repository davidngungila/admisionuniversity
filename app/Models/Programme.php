<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'department_id',
    'campus_id',
    'admission_level_id',
    'name',
    'code',
    'duration_years',
    'study_mode',
    'tuition_fee',
    'capacity',
    'description',
    'career_opportunities',
    'status',
])]
class Programme extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected function casts(): array
    {
        return [
            'duration_years' => 'integer',
            'tuition_fee' => 'decimal:2',
            'capacity' => 'integer',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function admissionLevel(): BelongsTo
    {
        return $this->belongsTo(AdmissionLevel::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(ProgrammeRequirement::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(ProgrammeCourse::class)->orderBy('year')->orderBy('semester')->orderBy('course_code');
    }

    protected function curriculum(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->courses->groupBy('year')->map(function ($yCourses, $year) {
                return $yCourses->groupBy('semester')->sortKeys();
            })->sortKeys(),
        )->withoutObjectCaching();
    }

    public function applicationProgrammes(): HasMany
    {
        return $this->hasMany(ApplicationProgramme::class);
    }
}