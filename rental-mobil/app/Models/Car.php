<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'brand',
        'size',
        'transmission',
        'plate_number',
        'price_per_hour',
        'status',
        'image',
    ];

    // Relasi: 1 Mobil bisa disewa berkali-kali (banyak Rental)
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
