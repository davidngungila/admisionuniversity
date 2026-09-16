<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'programme_id',
    'subject',
    'minimum_grade',
    'minimum_principal_passes',
    'requirement_text',
])]
class ProgrammeRequirement extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }
}