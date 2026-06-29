<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji pemuatan halaman login.
     */
    public function test_show_login_form(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /**
     * Uji pemuatan halaman registrasi.
     */
    public function test_show_register_form(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    /**
     * Uji registrasi user baru dengan data valid.
     */
    public function test_user_can_register(): void
    {
        $registrationData = [
            'name' => 'Budi Santoso',
            'email' => 'budi@gmail.com',
            'phone' => '08123456789',
            'password' => 'budi123',
        ];

        $response = $this->post('/register', $registrationData);

        // Harus mengalihkan ke halaman login dengan pesan sukses
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Pastikan user tersimpan di database
        $this->assertDatabaseHas('users', [
            'email' => 'budi@gmail.com',
            'name' => 'Budi Santoso',
            'phone' => '08123456789',
        ]);

        // Pastikan password di-hash dengan benar
        $user = User::where('email', 'budi@gmail.com')->first();
        $this->assertTrue(Hash::check('budi123', $user->password));
    }

    /**
     * Uji registrasi gagal ketika data tidak lengkap.
     */
    public function test_user_cannot_register_with_missing_fields(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'invalid-email',
            'phone' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'phone', 'password']);
        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Uji registrasi gagal dengan email duplikat.
     */
    public function test_user_cannot_register_with_duplicate_email(): void
    {
        // Buat user pertama terlebih dahulu
        User::create([
            'name' => 'Budi Lama',
            'email' => 'budi@gmail.com',
            'phone' => '0811111111',
            'password' => Hash::make('password123'),
        ]);

        // Coba daftarkan user kedua dengan email yang sama
        $response = $this->post('/register', [
            'name' => 'Budi Baru',
            'email' => 'budi@gmail.com',
            'phone' => '0822222222',
            'password' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseCount('users', 1);
    }

    /**
     * Uji login berhasil dengan kredensial yang valid.
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'name' => 'Admin Drivora',
            'email' => 'admin@drivora.com',
            'phone' => '0812345678',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@drivora.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Uji login gagal dengan sandi salah.
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::create([
            'name' => 'Admin Drivora',
            'email' => 'admin@drivora.com',
            'phone' => '0812345678',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@drivora.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Uji proses logout.
     */
    public function test_user_can_logout(): void
    {
        $user = User::create([
            'name' => 'Admin Drivora',
            'email' => 'admin@drivora.com',
            'phone' => '0812345678',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
