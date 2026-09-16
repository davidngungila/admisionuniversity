<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'academic_year_id',
    'application_round_id',
    'admission_level_id',
    'name',
    'status',
    'created_by',
    'processed_at',
    'remarks',
])]
class SelectionBatch extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function applicationRound(): BelongsTo
    {
        return $this->belongsTo(ApplicationRound::class);
    }

    public function admissionLevel(): BelongsTo
    {
        return $this->belongsTo(AdmissionLevel::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function results(): HasMany
    {
        return $this->hasMany(SelectionResult::class, 'selection_batch_id');
    }
}