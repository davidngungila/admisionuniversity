<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['application_id', 'workflow_step_id', 'completed_at'])]
class ApplicationStepCompletion extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(ApplicationWorkflowStep::class, 'workflow_step_id');
    }
}