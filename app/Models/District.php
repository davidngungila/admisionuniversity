<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['region_id', 'name', 'is_active'])]
class District extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class);
    }
}