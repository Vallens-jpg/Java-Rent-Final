<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    protected $fillable = [
        'user_id',
        'car_id',
        'start_time',
        'end_time',
        'total_price',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Relasi: Rental ini milik 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Rental ini menyewa 1 Mobil
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    // Relasi: 1 Rental bisa kena denda (Penalties)
    public function penalties()
    {
        return $this->hasMany(Penalty::class);
    }
}
