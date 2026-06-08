<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('cars.index');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public Car Routes
Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
Route::get('/cars/{car}', [CarController::class, 'show'])->name('cars.show');

// Customer / Authenticated Routes
// Customer / Authenticated Routes
Route::middleware('auth')->group(function () {
    // Auth routes (kosong sementara)
});

// Dipindah keluar auth agar user mudah test UI Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Rentals (Dikeluarkan dari auth sementara untuk kemudahan testing UI)
Route::get('/rentals/create/{car}', [RentalController::class, 'create'])->name('rentals.create');
Route::post('/rentals', [RentalController::class, 'store'])->name('rentals.store');
Route::get('/rentals/{rental}/payment', [RentalController::class, 'payment'])->name('rentals.payment');
Route::post('/rentals/{rental}/confirm', [RentalController::class, 'confirm'])->name('rentals.confirm');
Route::get('/rentals/{rental}/extend', [RentalController::class, 'extend'])->name('rentals.extend');
Route::post('/rentals/{rental}/extend', [RentalController::class, 'submitExtend'])->name('rentals.submit_extend');
Route::post('/rentals/{rental}/pay-extend', [RentalController::class, 'payExtend'])->name('rentals.pay_extend');
Route::get('/rentals/{rental}/penalty', [RentalController::class, 'penalty'])->name('rentals.penalty');
Route::get('/rentals/{rental}/api-status', [RentalController::class, 'apiCheckStatus'])->name('api.rentals.status');
Route::post('/rentals/{rental}/pay-penalty', [RentalController::class, 'payPenalty'])->name('rentals.pay_penalty');
Route::post('/rentals/{rental}/dismiss-notification', [RentalController::class, 'dismissNotification'])->name('rentals.dismiss_notification');
Route::get('/rentals/{rental}', [RentalController::class, 'show'])->name('rentals.show');

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminIndex'])->name('dashboard');
    
    // Cars CRUD (Admin Only)
    Route::resource('cars', CarController::class)->except(['index', 'show']);
    
    // Rentals Management
    Route::get('/rentals', [RentalController::class, 'adminIndex'])->name('rentals.index');
    Route::patch('/rentals/{rental}/status', [RentalController::class, 'updateStatus'])->name('rentals.update_status');
});
