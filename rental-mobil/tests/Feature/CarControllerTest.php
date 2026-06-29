<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Car;
use Tests\TestCase;

class CarControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji memuat halaman utama daftar mobil.
     */
    public function test_cars_index_page_returns_success_and_lists_cars(): void
    {
        $car = Car::create([
            'brand' => 'Toyota Avanza',
            'plate_number' => 'B 1234 CD',
            'transmission' => 'Manual',
            'size' => 'Medium',
            'price_per_hour' => 50000,
            'status' => 'available',
            'image' => 'avanza.png'
        ]);

        $response = $this->get('/cars');

        $response->assertStatus(200);
        $response->assertViewIs('cars.index');
        $response->assertSee('Toyota Avanza');
    }

    /**
     * Uji pemfilteran daftar mobil berdasarkan merk, ukuran, dan transmisi.
     */
    public function test_cars_index_page_can_be_filtered(): void
    {
        // Mobil 1
        Car::create([
            'brand' => 'Toyota Avanza',
            'plate_number' => 'B 1234 CD',
            'transmission' => 'Manual',
            'size' => 'Medium',
            'price_per_hour' => 50000,
            'status' => 'available',
            'image' => ''
        ]);

        // Mobil 2
        Car::create([
            'brand' => 'Honda Civic',
            'plate_number' => 'B 5678 EF',
            'transmission' => 'Automatic',
            'size' => 'Small',
            'price_per_hour' => 80000,
            'status' => 'available',
            'image' => ''
        ]);

        // Filter pencarian "Civic"
        $response = $this->get('/cars?search=Civic');
        $response->assertSee('Honda Civic');
        $response->assertDontSee('Toyota Avanza');

        // Filter transmisi "Manual"
        $response = $this->get('/cars?transmission=Manual');
        $response->assertSee('Toyota Avanza');
        $response->assertDontSee('Honda Civic');
    }

    /**
     * Uji pemuatan detail mobil yang ada di database.
     */
    public function test_car_show_page_returns_success_for_existing_car(): void
    {
        $car = Car::create([
            'brand' => 'Toyota Avanza',
            'plate_number' => 'B 1234 CD',
            'transmission' => 'Manual',
            'size' => 'Medium',
            'price_per_hour' => 50000,
            'status' => 'available',
            'image' => ''
        ]);

        $response = $this->get("/cars/{$car->id}");

        $response->assertStatus(200);
        $response->assertViewIs('cars.show');
        $response->assertSee('Toyota Avanza');
        $response->assertSee('B 1234 CD');
    }

    /**
     * Uji pemuatan mobil yang tidak ada di database menghasilkan error 404.
     */
    public function test_car_show_page_returns_404_for_non_existent_car(): void
    {
        $response = $this->get('/cars/999');

        $response->assertStatus(404);
    }
}
