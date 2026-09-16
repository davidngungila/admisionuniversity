<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'location', 'description', 'is_active'])]
class Campus extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public function programmes(): HasMany
    {
        return $this->hasMany(Programme::class);
    }
}