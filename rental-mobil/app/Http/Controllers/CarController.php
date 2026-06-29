<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::query();
        
        // Filter DB
        if ($request->filled('size')) {
            $query->where('size', $request->size);
        }
        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }
        if ($request->filled('search')) {
            $query->where('brand', 'like', '%' . $request->search . '%');
        }

        // Tampilkan yang available dulu, baru yang sedang disewa
        $query->orderByRaw("CASE WHEN status = 'available' THEN 1 WHEN status = 'rented' THEN 2 ELSE 3 END");
        $cars = $query->get();

        return view('cars.index', compact('cars'));
    }

    public function show($id)
    {
        $car = Car::find($id);

        if (!$car) {
            abort(404, 'Mobil tidak ditemukan');
        }

        return view('cars.show', compact('car'));
    }

    // --- Admin Methods ---
    public function create()
    {
        // return view('admin.cars.create');
    }

    public function store(Request $request)
    {
        // Store logic
    }

    public function edit(Car $car)
    {
        // return view('admin.cars.edit', compact('car'));
    }

    public function update(Request $request, Car $car)
    {
        // Update logic
    }

    public function destroy(Car $car)
    {
        // Delete logic
    }
}
