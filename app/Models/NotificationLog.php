<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'application_id',
    'channel',
    'template',
    'recipient',
    'subject',
    'body',
    'status',
    'sent_at',
    'is_read',
])]
class NotificationLog extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'notifications_logs';

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'is_read' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}