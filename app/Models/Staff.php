<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes;

    protected $table = 'staff';

    protected $fillable = [
        'branch_id',
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
        'two_fa_enabled',
        'two_fa_secret',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'two_fa_secret',
    ];

    protected function casts(): array
    {
        return [
            'is_active'      => 'boolean',
            'two_fa_enabled' => 'boolean',
            'last_login'     => 'datetime',
            'password'       => 'hashed',
        ];
    }

    // JWT required methods
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'role'  => $this->role,
            'email' => $this->email,
            'type'  => 'staff',
            'branch_id' => $this->branch_id,
        ];
    }

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
