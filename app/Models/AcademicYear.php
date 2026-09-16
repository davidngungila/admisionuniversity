<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['name', 'start_date', 'end_date', 'is_active', 'status'])]
class AcademicYear extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(ApplicationRound::class);
    }

    public function admissionWindows(): HasMany
    {
        return $this->hasMany(AdmissionWindow::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public static function current(): ?self
    {
        return static::where('is_active', true)->first() ?? static::latest('id')->first();
    }
}