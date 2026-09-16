<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'nationality', 'is_active'])]
class Country extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'citizenship_id');
    }
}