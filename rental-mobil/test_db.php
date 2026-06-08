<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$r = new App\Models\Rental();
$r->user_id = 3; // Areta
$r->car_id = App\Models\Car::first()->id ?? 1;
$r->start_time = now();
$r->end_time = now()->addDays(2);
$r->total_price = 150000;
$r->status = 'pending';
$r->save();

echo "Inserted Rental ID: {$r->id}\n";
