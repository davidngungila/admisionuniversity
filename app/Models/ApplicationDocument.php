<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'application_id',
    'workflow_step_id',
    'document_type',
    'file_path',
    'file_name',
    'file_size',
    'mime_type',
    'is_verified',
    'verified_by',
    'verified_at',
    'verification_notes',
])]
class ApplicationDocument extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(ApplicationWorkflowStep::class, 'workflow_step_id');
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}