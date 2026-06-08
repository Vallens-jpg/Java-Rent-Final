<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rentals = App\Models\Rental::all();
echo "Rentals count: " . $rentals->count() . "\n";
foreach ($rentals as $r) {
    echo "ID: {$r->id}, Status: {$r->status}, User: {$r->user_id}, Admin: {$r->admin_id}, Reason: {$r->rejection_reason}\n";
}
