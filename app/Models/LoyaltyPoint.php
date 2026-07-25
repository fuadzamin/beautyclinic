<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points_earned',
        'total_points',
        'source',
        'source_id',
    ];

    protected function casts(): array
    {
        return [
            'points_earned' => 'integer',
            'total_points'  => 'integer',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
