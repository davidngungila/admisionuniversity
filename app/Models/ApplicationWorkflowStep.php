<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'admission_level_id',
    'step_number',
    'step_name',
    'route',
    'icon',
    'description',
    'is_required',
    'is_active',
])]
class ApplicationWorkflowStep extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public const STEP_PERSONAL_INFO = 'personal-info';
    public const STEP_PAYMENT = 'payment';
    public const STEP_ACADEMIC_RESULTS = 'academic-results';
    public const STEP_PROGRAMME = 'programme-application';
    public const STEP_DOCUMENTS = 'documents';
    public const STEP_SUBMIT = 'submit';

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function admissionLevel(): BelongsTo
    {
        return $this->belongsTo(AdmissionLevel::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(ApplicationStepCompletion::class, 'workflow_step_id');
    }
}