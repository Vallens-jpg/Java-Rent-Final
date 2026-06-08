<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function store(Request $request)
    {
        // Rental creation logic
    }

    public function show(Rental $rental)
    {
        return view('rentals.show', compact('rental'));
    }

    // --- Admin Methods ---
    public function adminIndex()
    {
        $rentals = Rental::with(['user', 'car'])->latest()->get();
        return view('admin.rentals.index', compact('rentals'));
    }

    public function updateStatus(Request $request, Rental $rental)
    {
        // Update status logic
    }
}
