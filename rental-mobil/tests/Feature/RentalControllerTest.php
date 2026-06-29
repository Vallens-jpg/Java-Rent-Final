<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Car;
use App\Models\User;
use App\Models\Rental;
use Tests\TestCase;

class RentalControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Car $car;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat user untuk login sewa
        $this->user = User::create([
            'name' => 'John Doe',
            'email' => 'john@gmail.com',
            'phone' => '0812345678',
            'password' => bcrypt('password123'),
        ]);

        // Buat data mobil
        $this->car = Car::create([
            'brand' => 'Toyota Avanza',
            'plate_number' => 'B 1234 CD',
            'transmission' => 'Manual',
            'size' => 'Medium',
            'price_per_hour' => 100000,
            'status' => 'available',
            'image' => ''
        ]);
    }

    /**
     * Uji memuat halaman buat pesanan rental mobil.
     */
    public function test_create_rental_form_renders_successfully(): void
    {
        $response = $this->get("/rentals/create/{$this->car->id}");

        $response->assertStatus(200);
        $response->assertViewIs('rentals.create');
        $response->assertSee('Toyota Avanza');
    }

    /**
     * Uji validasi gagal saat data pemesanan kosong.
     */
    public function test_store_rental_validation_fails_with_empty_inputs(): void
    {
        $response = $this->post('/rentals', [
            'car_id' => '',
            'duration' => '',
            'phone' => '',
            'ktp' => '',
            'address' => '',
        ]);

        $response->assertSessionHasErrors(['car_id', 'duration', 'phone', 'ktp', 'address']);
    }

    /**
     * Uji penyimpanan rental baru berhasil, termasuk kalkulasi diskon untuk durasi tertentu.
     */
    public function test_store_rental_successfully_creates_rental_with_discount_calculation(): void
    {
        // 1. Simpan sewa reguler selama 2 hari (Tarif per hari = 100.000 * 24 = 2.400.000)
        // Total untuk 2 hari = 4.800.000
        $response = $this->actingAs($this->user)->post('/rentals', [
            'car_id' => $this->car->id,
            'duration' => 2,
            'phone' => '08122334455',
            'ktp' => '3201234567890001',
            'address' => 'Jl. Kebon Jeruk No 5',
        ]);

        $this->assertDatabaseHas('rentals', [
            'user_id' => $this->user->id,
            'car_id' => $this->car->id,
            'total_price' => 4800000,
            'status' => 'pending'
        ]);

        // 2. Simpan sewa promo 7 hari (Mendapat diskon promo 15%)
        // Total normal = 100.000 * 24 * 7 = 16.800.000
        // Setelah diskon 15% = 16.800.000 * 0.85 = 14.280.000
        $responsePromo = $this->actingAs($this->user)->post('/rentals', [
            'car_id' => $this->car->id,
            'duration' => 7,
            'phone' => '08122334455',
            'ktp' => '3201234567890001',
            'address' => 'Jl. Kebon Jeruk No 5',
        ]);

        $this->assertDatabaseHas('rentals', [
            'user_id' => $this->user->id,
            'car_id' => $this->car->id,
            'total_price' => 14280000,
            'status' => 'pending'
        ]);
    }

    /**
     * Uji memuat halaman pembayaran rental.
     */
    public function test_payment_page_renders_successfully(): void
    {
        $rental = Rental::create([
            'user_id' => $this->user->id,
            'car_id' => $this->car->id,
            'start_time' => now(),
            'end_time' => now()->addDays(2),
            'total_price' => 4800000,
            'status' => 'pending'
        ]);

        $response = $this->get("/rentals/{$rental->id}/payment");

        $response->assertStatus(200);
        $response->assertViewIs('rentals.payment');
        $response->assertSee('4.800.000');
    }

    /**
     * Uji memuat halaman perpanjangan sewa mobil.
     */
    public function test_extend_rental_form_renders_successfully(): void
    {
        $rental = Rental::create([
            'user_id' => $this->user->id,
            'car_id' => $this->car->id,
            'start_time' => now(),
            'end_time' => now()->addDays(2),
            'total_price' => 4800000,
            'status' => 'active'
        ]);

        $response = $this->get("/rentals/{$rental->id}/extend");

        $response->assertStatus(200);
        $response->assertViewIs('rentals.extend');
    }

    /**
     * Uji pengajuan perpanjangan sewa menghitung nominal harga tambahan.
     */
    public function test_submit_extend_rental_calculates_and_sets_unpaid_extension(): void
    {
        $rental = Rental::create([
            'user_id' => $this->user->id,
            'car_id' => $this->car->id,
            'start_time' => now(),
            'end_time' => now()->addDays(2),
            'total_price' => 4800000,
            'status' => 'active'
        ]);

        // Perpanjang 1 Hari (Tarif tambahan = 100.000 * 24 * 1 = 2.400.000)
        $response = $this->actingAs($this->user)->post("/rentals/{$rental->id}/extend", [
            'extend_days' => 1
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('rentals.extend_payment');
        $response->assertSee('2.400.000');

        $this->assertDatabaseHas('rentals', [
            'id' => $rental->id,
            'extension_days' => 1,
            'extension_status' => 'unpaid'
        ]);
    }

    /**
     * Uji pembayaran perpanjangan mengubah status menunggu verifikasi admin.
     */
    public function test_pay_extend_updates_status_to_pending_verification(): void
    {
        $rental = new Rental();
        $rental->user_id = $this->user->id;
        $rental->car_id = $this->car->id;
        $rental->start_time = now();
        $rental->end_time = now()->addDays(2);
        $rental->total_price = 4800000;
        $rental->status = 'active';
        $rental->extension_days = 1;
        $rental->extension_status = 'unpaid';
        $rental->save();

        $response = $this->actingAs($this->user)->post("/rentals/{$rental->id}/pay-extend");

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('rentals', [
            'id' => $rental->id,
            'extension_status' => 'pending_verification',
            'notification_dismissed' => 0
        ]);
    }

    /**
     * Uji menyembunyikan notifikasi sewa.
     */
    public function test_dismiss_notification_updates_rental_correctly(): void
    {
        $rental = new Rental();
        $rental->user_id = $this->user->id;
        $rental->car_id = $this->car->id;
        $rental->start_time = now();
        $rental->end_time = now()->addDays(2);
        $rental->total_price = 4800000;
        $rental->status = 'active';
        $rental->notification_dismissed = 0;
        $rental->save();

        $response = $this->actingAs($this->user)->post("/rentals/{$rental->id}/dismiss-notification");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $this->assertDatabaseHas('rentals', [
            'id' => $rental->id,
            'notification_dismissed' => 1
        ]);
    }

    /**
     * Uji API status pengembalian.
     */
    public function test_api_check_status_returns_correct_json_response(): void
    {
        $rental = new Rental();
        $rental->user_id = $this->user->id;
        $rental->car_id = $this->car->id;
        $rental->start_time = now();
        $rental->end_time = now()->addDays(2);
        $rental->total_price = 4800000;
        $rental->status = 'active';
        $rental->penalty_status = 'unpaid';
        $rental->extension_status = 'none';
        $rental->save();

        $response = $this->get("/rentals/{$rental->id}/api-status");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'active',
            'penalty_status' => 'unpaid',
            'extension_status' => 'none'
        ]);
    }
}
