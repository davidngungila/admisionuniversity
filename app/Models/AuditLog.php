<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'action',
    'method',
    'url',
    'response_status',
    'model_type',
    'model_id',
    'before',
    'after',
    'ip_address',
    'user_agent',
])]
class AuditLog extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected function casts(): array
    {
        return [
            'before'          => 'array',
            'after'           => 'array',
            'response_status' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(
        string $action,
        ?int $userId,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $before = null,
        ?array $after = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?string $method = null,
        ?string $url = null,
        ?int $responseStatus = null,
    ): static {
        return static::create([
            'action'          => $action,
            'user_id'         => $userId,
            'method'          => $method,
            'url'             => $url,
            'response_status' => $responseStatus,
            'model_type'      => $modelType,
            'model_id'        => $modelId,
            'before'          => $before,
            'after'           => $after,
            'ip_address'      => $ipAddress,
            'user_agent'      => $userAgent,
        ]);
    }
}