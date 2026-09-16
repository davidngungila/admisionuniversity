<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'selection_batch_id',
    'application_id',
    'programme_id',
    'status',
    'rank_score',
    'position',
    'remarks',
])]
class SelectionResult extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public function selectionBatch(): BelongsTo
    {
        return $this->belongsTo(SelectionBatch::class, 'selection_batch_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }
}