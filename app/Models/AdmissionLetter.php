<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'application_id',
    'selection_result_id',
    'letter_number',
    'content',
    'file_path',
    'status',
    'issued_at',
    'downloaded_at',
])]
class AdmissionLetter extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'downloaded_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function selectionResult(): BelongsTo
    {
        return $this->belongsTo(SelectionResult::class);
    }
}