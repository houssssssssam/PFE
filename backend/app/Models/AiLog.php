<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiLog extends Model
{
    protected $fillable = [
        'conversation_id',
        'message_id',
        'workflow',
        'prompt',
        'response',
        'model',
        'confidence',
        'tokens_used',
        'duration_ms',
        'escalated',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'decimal:2',
            'escalated' => 'boolean',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
