<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    protected $fillable = [
        'rental_id',
        'delay_duration',
        'penalty_amount',
        'status',
    ];

    // Relasi: Denda ini terkait dengan 1 Rental
    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}
