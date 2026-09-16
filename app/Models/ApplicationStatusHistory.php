<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['application_id', 'old_status', 'new_status', 'changed_by', 'remarks'])]
class ApplicationStatusHistory extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}