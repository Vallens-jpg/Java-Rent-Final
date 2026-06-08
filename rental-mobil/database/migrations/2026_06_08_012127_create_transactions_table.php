<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained('rentals')->onDelete('cascade');
            $table->foreignId('car_id')->constrained('cars')->onDelete('cascade');
            $table->enum('transaction_type', ['Sewa Baru', 'Perpanjangan', 'Denda']);
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });

        // Migrate existing completed rentals as 'Sewa Baru'
        DB::statement("
            INSERT INTO transactions (rental_id, car_id, transaction_type, amount, created_at, updated_at)
            SELECT id, car_id, 'Sewa Baru', total_price, updated_at, updated_at
            FROM rentals
            WHERE status = 'completed'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
