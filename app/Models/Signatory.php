<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'title',
    'designation',
    'signature_image',
    'order_index',
    'is_active',
])]
class Signatory extends Model
{
    public const path = 'signatures';

    protected function casts(): array
    {
        return [
            'order_index' => 'integer',
            'is_active'   => 'boolean',
        ];
    }

    public function getSignatureUrlAttribute(): ?string
    {
        return $this->signature_image ? url($this->signature_image) : null;
    }
}