<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['application_id', 'programme_id', 'preference_order', 'status'])]
class ApplicationProgramme extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }
}