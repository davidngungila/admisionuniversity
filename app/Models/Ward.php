<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['district_id', 'name', 'is_active'])]
class Ward extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}