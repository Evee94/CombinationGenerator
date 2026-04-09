<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GenerationHistory extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'parameters',
        'combinations',
        'combination_count',
    ];

    protected function casts(): array
    {
        return [
            'parameters' => 'array',
            'combinations' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
