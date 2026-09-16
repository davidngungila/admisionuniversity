<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'short_name', 'sort_order', 'is_active'])]
class AdmissionLevel extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function programmes(): HasMany
    {
        return $this->hasMany(Programme::class);
    }

    public function workflowSteps(): HasMany
    {
        return $this->hasMany(ApplicationWorkflowStep::class);
    }

    public function admissionWindows(): HasMany
    {
        return $this->hasMany(AdmissionWindow::class);
    }
}