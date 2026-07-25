<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    
    protected $fillable = [
        'name',
        'address',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function treatments()
    {
        return $this->belongsToMany(Treatment::class, 'branch_treatment');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'branch_product')
                    ->withPivot('stock_quantity')
                    ->withTimestamps();
    }
}
