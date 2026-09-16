<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable([
    'academic_year_id',
    'round_number',
    'name',
    'description',
    'is_current',
    'allow_multiple_applications',
    'is_active',
])]
class ApplicationRound extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'allow_multiple_applications' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function admissionWindows(): HasMany
    {
        return $this->hasMany(AdmissionWindow::class);
    }

    public function applications(): HasMany
    {
        return $this->hasManyThrough(Application::class, AdmissionWindow::class);
    }

    public function selectionBatches(): HasMany
    {
        return $this->hasMany(SelectionBatch::class);
    }
}