<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Treatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'benefits',
        'category',
        'price',
        'duration_minutes',
        'image_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price'            => 'integer',
            'duration_minutes' => 'integer',
            'is_active'        => 'boolean',
        ];
    }

    // Relationships
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
