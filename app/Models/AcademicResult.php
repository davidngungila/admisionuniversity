<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'application_id',
    'exam_type',
    'exam_body',
    'index_number',
    'school_name',
    'exam_year',
    'results',
    'overall_grade',
    'total_subjects',
    'passes_count',
    'is_verified',
    'verified_by',
    'verified_at',
])]
class AcademicResult extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected function casts(): array
    {
        return [
            'results' => 'array',
            'exam_year' => 'integer',
            'total_subjects' => 'integer',
            'passes_count' => 'integer',
            'is_verified' => 'boolean',
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
}