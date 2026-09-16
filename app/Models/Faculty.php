<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'description', 'is_active'])]
class Faculty extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }
}