<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpertDocument extends Model
{
    protected $fillable = ['expert_id', 'type', 'file_url', 'original_name', 'verified'];

    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'verified' => 'boolean',
        ];
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class);
    }
}
