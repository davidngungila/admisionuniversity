<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgrammeCourse extends Model
{
    protected $fillable = ['programme_id', 'year', 'semester', 'course_code', 'course_name', 'credit_hours'];

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }
}
