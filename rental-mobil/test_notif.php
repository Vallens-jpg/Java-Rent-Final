<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate auth()->id() = 3
$notifications = \App\Models\Rental::with('car')->where('user_id', 3)
    ->whereIn('status', ['rejected', 'active'])
    ->orderBy('updated_at', 'desc')
    ->get();

echo "Notif count: " . $notifications->count() . "\n";
foreach($notifications as $n) {
    echo "ID: {$n->id}, Status: {$n->status}\n";
}
